<?php
namespace App\Model;

class Exposee extends AbstractModel {
    private $key;
    private $keyDate;
    private $receivedAt;

    /**
     * Parse Base64-encoded key
     * @param  string      $b64Key Exposee key, Base64-encoded
     * @return string|null         Raw secret key or NULL in case of error
     */
    public static function parseKey(string $b64Key): ?string {
        $key = base64_decode($b64Key, true);
        if (($key === false) || (strlen($key) != 32)) return null;
        return $key;
    }


    /**
     * Is valid key date
     * @param  string  $keyDate Key date in YYYY-MM-DD format
     * @return boolean          Is valid date
     */
    public static function isValidKeyDate(string $keyDate): bool {
        // Looks like a date?
        if (!preg_match('/[0-9\-]{10}/', $keyDate)) return false;

        // Is date in between allowed range?
        $maxDate = strtotime('today +1 day');
        $minDate = strtotime('today -30 days');
        $time = strtotime($keyDate);
        if (($time < $minDate) || ($time > $maxDate)) return false;

        // Looks good
        return true;
    }


    /**
     * Class constructor
     * @param string $key        Raw secret key (32 bytes long)
     * @param string $keyDate    Key date in YYYY-MM-DD format
     * @param int    $receivedAt Date of receipt as UNIX timestamp
     */
    public function __construct(string $key, string $keyDate, int $receivedAt) {
        $this->key = $key;
        $this->keyDate = $keyDate;
        $this->receivedAt = $receivedAt;
    }


    /**
     * Get secret key
     * @return string Raw secret key (32 bytes long)
     */
    public function getKey(): string {
        return $this->key;
    }


    /**
     * Get key date
     * @return string Key date in YYYY-MM-DD format
     */
    public function getKeyDate(): string {
        return $this->keyDate;
    }


    /**
     * Get received at
     * @return int Date of receipt as UNIX timestamp
     */
    public function getReceivedAt(): int {
        return $this->receivedAt;
    }


    /**
     * @inheritdoc
     */
    public function getDigest(): string {
        return $this->key;
    }
}
