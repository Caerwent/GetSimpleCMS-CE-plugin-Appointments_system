<?php
if (!defined('IN_GS')) { die('You cannot load this page directly.'); }

/**
 * Database management class
 */
class AppointmentDatabase {
    private static $instance = null;
    private $db;

    private function __construct() {
        try {
            // Vérifier que le dossier existe
            if (!file_exists(GSDATAOTHERPATH)) {
                mkdir(GSDATAOTHERPATH, 0755, true);
            }

            // Créer ou ouvrir la base de données
            $this->db = new SQLite3(APPOINTMENT_DB);
            $this->db->busyTimeout(5000);

            // Créer les tables
            $this->createTables();
        } catch (Exception $e) {
            error_log('AppointmentDatabase Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->db;
    }

    private function createTables() {
        // Table des créneaux horaires
        $this->db->exec("
        CREATE TABLE IF NOT EXISTS time_slots (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            day_of_week INTEGER NOT NULL,
            start_time TEXT NOT NULL,
            end_time TEXT NOT NULL,
            duration INTEGER NOT NULL,
            max_appointments INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        ");

        // Table des rendez-vous
        $this->db->exec("
        CREATE TABLE IF NOT EXISTS appointments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            appointment_date DATE NOT NULL,
            start_time TEXT NOT NULL,
            end_time TEXT NOT NULL,
            client_name TEXT NOT NULL,
            client_email TEXT NOT NULL,
            client_phone TEXT,
            notes TEXT,
            status TEXT DEFAULT 'confirmed',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        ");

        // Table des exceptions (jours fermés)
        $this->db->exec("
        CREATE TABLE IF NOT EXISTS exceptions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            exception_date DATE NOT NULL UNIQUE,
            reason TEXT,
            duration INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        ");

        // Table des paramètres
        $this->db->exec("
        CREATE TABLE IF NOT EXISTS settings (
            setting_key TEXT PRIMARY KEY,
            setting_value TEXT,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        ");
    }
}

/**
 * TimeSlot Model
 */
class AppointmentTimeSlot {

    public static function create($dayOfWeek, $startTime, $endTime, $duration, $maxAppointments) {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();

            $stmt = $db->prepare("
            INSERT INTO time_slots (day_of_week, start_time, end_time, duration, max_appointments)
            VALUES (:day, :start, :end, :duration, :max)
            ");

            $stmt->bindValue(':day', $dayOfWeek, SQLITE3_INTEGER);
            $stmt->bindValue(':start', $startTime, SQLITE3_TEXT);
            $stmt->bindValue(':end', $endTime, SQLITE3_TEXT);
            $stmt->bindValue(':duration', $duration, SQLITE3_INTEGER);
            $stmt->bindValue(':max', $maxAppointments, SQLITE3_INTEGER);

            return $stmt->execute() !== false;
        } catch (Exception $e) {
            error_log('AppointmentTimeSlot::create Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getAll() {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();
            $result = $db->query("SELECT * FROM time_slots ORDER BY day_of_week, start_time");

            $slots = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $slots[] = $row;
            }

            return $slots;
        } catch (Exception $e) {
            error_log('AppointmentTimeSlot::getAll Error: ' . $e->getMessage());
            return [];
        }
    }

    public static function delete($id) {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM time_slots WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            return $stmt->execute() !== false;
        } catch (Exception $e) {
            error_log('AppointmentTimeSlot::delete Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getAvailableSlots($date) {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();

            // Vérifier si c'est un jour d'exception
            $stmt = $db->prepare("SELECT COUNT(*) as count, duration FROM exceptions WHERE exception_date = :date");
            $stmt->bindValue(':date', $date, SQLITE3_TEXT);
            $result = $stmt->execute();
            $exceptionDays = $result->fetchArray(SQLITE3_ASSOC);

            $exception = 0;
            if ($exceptionDays['count'] > 0) {
                if($exceptionDays['duration']==0) {
                    return [];
                } else {
                    $exception = $exceptionDays['duration'];
                }

            }

            // Obtenir le jour de la semaine (1 = lundi, 7 = dimanche)
            $dayOfWeek = date('N', strtotime($date));

            // Récupérer les créneaux pour ce jour
            $stmt = $db->prepare("SELECT * FROM time_slots WHERE day_of_week = :day ORDER BY start_time");
            $stmt->bindValue(':day', $dayOfWeek, SQLITE3_INTEGER);
            $result = $stmt->execute();

            $availableSlots = [];

            while ($slot = $result->fetchArray(SQLITE3_ASSOC)) {

                // Générer les créneaux basés sur la durée
                $start = strtotime($slot['start_time']);
                $end = strtotime($slot['end_time']);

                $duration = $slot['duration'] * 60; // en secondes

                $current = $start;


                while ($current + $duration <= $end) {
                    $slotStart = date('H:i', $current);
                    $slotEnd = date('H:i', $current + $duration);
                    $hour = date('H', $current);
                    if(($hour<12 && $exception!=1) ||
                        ($hour>=12 && $exception!=2)) {

                        // Compter les rendez-vous existants pour ce créneau
                        $countStmt = $db->prepare("
                        SELECT COUNT(*) as count
                        FROM appointments
                        WHERE appointment_date = :date
                        AND start_time = :start
                        AND status = 'confirmed'
                        ");
                        $countStmt->bindValue(':date', $date, SQLITE3_TEXT);
                        $countStmt->bindValue(':start', $slotStart, SQLITE3_TEXT);
                        $countResult = $countStmt->execute();
                        $countRow = $countResult->fetchArray(SQLITE3_ASSOC);

                        $booked = $countRow['count'];
                        $available = $slot['max_appointments'] - $booked;

                        if ($available > 0) {
                            $availableSlots[] = [
                                'start' => $slotStart,
                                'end' => $slotEnd,
                                'available' => $available
                            ];
                        }
                    }

                    $current += $duration;
                }
            }

            return $availableSlots;
        } catch (Exception $e) {
            error_log('AppointmentTimeSlot::getAvailableSlots Error: ' . $e->getMessage());
            return [];
        }
    }
}

/**
 * Booking Model
 */
class AppointmentBooking {

    public static function create($date, $startTime, $endTime, $name, $email, $phone = '', $notes = '') {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();

            $stmt = $db->prepare("
            INSERT INTO appointments (appointment_date, start_time, end_time, client_name, client_email, client_phone, notes, status)
            VALUES (:date, :start, :end, :name, :email, :phone, :notes, 'confirmed')
            ");

            $stmt->bindValue(':date', $date, SQLITE3_TEXT);
            $stmt->bindValue(':start', $startTime, SQLITE3_TEXT);
            $stmt->bindValue(':end', $endTime, SQLITE3_TEXT);
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
            $stmt->bindValue(':notes', $notes, SQLITE3_TEXT);

            return $stmt->execute() !== false;
        } catch (Exception $e) {
            error_log('AppointmentBooking::create Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getAll($fromDate = null) {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();

            if ($fromDate) {
                $stmt = $db->prepare("
                SELECT * FROM appointments
                WHERE appointment_date >= :date
                ORDER BY appointment_date, start_time
                ");
                $stmt->bindValue(':date', $fromDate, SQLITE3_TEXT);
                $result = $stmt->execute();
            } else {
                $result = $db->query("SELECT * FROM appointments ORDER BY appointment_date DESC, start_time");
            }

            $appointments = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $appointments[] = $row;
            }

            return $appointments;
        } catch (Exception $e) {
            error_log('AppointmentBooking::getAll Error: ' . $e->getMessage());
            return [];
        }
    }

    public static function cancel($id) {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            return $stmt->execute() !== false;
        } catch (Exception $e) {
            error_log('AppointmentBooking::cancel Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function delete($id) {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM appointments WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            return $stmt->execute() !== false;
        } catch (Exception $e) {
            error_log('AppointmentBooking::delete Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getStats() {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();

            $stats = [
                'total' => 0,
                'today' => 0,
                'upcoming' => 0,
                'cancelled' => 0
            ];

            // Total
            $result = $db->query("SELECT COUNT(*) as count FROM appointments");
            $row = $result->fetchArray(SQLITE3_ASSOC);
            $stats['total'] = $row['count'];

            // Today
            $today = date('Y-m-d');
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE appointment_date = :date AND status = 'confirmed'");
            $stmt->bindValue(':date', $today, SQLITE3_TEXT);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_ASSOC);
            $stats['today'] = $row['count'];

            // Upcoming
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE appointment_date > :date AND status = 'confirmed'");
            $stmt->bindValue(':date', $today, SQLITE3_TEXT);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_ASSOC);
            $stats['upcoming'] = $row['count'];

            // Cancelled
            $result = $db->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'cancelled'");
            $row = $result->fetchArray(SQLITE3_ASSOC);
            $stats['cancelled'] = $row['count'];

            return $stats;
        } catch (Exception $e) {
            error_log('AppointmentBooking::getStats Error: ' . $e->getMessage());
            return [
                'total' => 0,
                'today' => 0,
                'upcoming' => 0,
                'cancelled' => 0
            ];
        }
    }
}

/**
 * Exception Model
 */
class AppointmentException {

    public static function create($date, $reason = '', $duration=0) {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();

            $stmt = $db->prepare("
            INSERT OR REPLACE INTO exceptions (exception_date, reason, duration)
            VALUES (:date, :reason, :duration)
            ");

            $stmt->bindValue(':date', $date, SQLITE3_TEXT);
            $stmt->bindValue(':reason', $reason, SQLITE3_TEXT);
            $stmt->bindValue(':duration', $duration, SQLITE3_INTEGER);

            return $stmt->execute() !== false;
        } catch (Exception $e) {
            error_log('AppointmentException::create Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getAll() {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();
            $result = $db->query("SELECT * FROM exceptions ORDER BY exception_date");

            $exceptions = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $exceptions[] = $row;
            }

            return $exceptions;
        } catch (Exception $e) {
            error_log('AppointmentException::getAll Error: ' . $e->getMessage());
            return [];
        }
    }

    public static function delete($id) {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM exceptions WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            return $stmt->execute() !== false;
        } catch (Exception $e) {
            error_log('AppointmentException::delete Error: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * Settings Model
 */
class AppointmentSettings {

    public static function get($key, $default = '') {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();

            $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = :key");
            $stmt->bindValue(':key', $key, SQLITE3_TEXT);
            $result = $stmt->execute();

            $row = $result->fetchArray(SQLITE3_ASSOC);

            return $row ? $row['setting_value'] : $default;
        } catch (Exception $e) {
            error_log('AppointmentSettings::get Error: ' . $e->getMessage());
            return $default;
        }
    }

    public static function set($key, $value) {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();

            $stmt = $db->prepare("
            INSERT OR REPLACE INTO settings (setting_key, setting_value, updated_at)
            VALUES (:key, :value, CURRENT_TIMESTAMP)
            ");

            $stmt->bindValue(':key', $key, SQLITE3_TEXT);
            $stmt->bindValue(':value', $value, SQLITE3_TEXT);

            return $stmt->execute() !== false;
        } catch (Exception $e) {
            error_log('AppointmentSettings::set Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getAll() {
        try {
            $db = AppointmentDatabase::getInstance()->getConnection();
            $result = $db->query("SELECT * FROM settings");

            $settings = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }

            return $settings;
        } catch (Exception $e) {
            error_log('AppointmentSettings::getAll Error: ' . $e->getMessage());
            return [];
        }
    }
}
