<?php
namespace App\Model;

class Exposee extends AbstractModel {
    private $key;
    private $onset;
    private $uploadedAt;

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
     * Is valid onset
     * @param  string  $onset Date in YYYY-MM-DD format
     * @return boolean        Is valid date
     */
    public static function isValidOnset(string $onset): bool {
        // Looks like a date?
        if (!preg_match('/[0-9\-]{10}/', $onset)) return false;

        // Is date in between allowed range?
        $maxDate = strtotime('today +1 day');
        $minDate = strtotime('today -30 days');
        $time = strtotime($onset);
        if (($time < $minDate) || ($time > $maxDate)) return false;

        // Looks good
        return true;
    }


    /**
     * Class constructor
     * @param string $key        Raw secret key (32 bytes long)
     * @param string $onset      Date in YYYY-MM-DD format
     * @param int    $uploadedAt Upload date (UNIX timestamp)
     */
    public function __construct(string $key, string $onset, int $uploadedAt) {
        $this->key = $key;
        $this->onset = $onset;
        $this->uploadedAt = $uploadedAt;
    }


    /**
     * Get secret key
     * @return string Raw secret key (32 bytes long)
     */
    public function getKey(): string {
        return $this->key;
    }


    /**
     * Get onset date
     * @return string Date in YYYY-MM-DD format
     */
    public function getOnset(): string {
        return $this->onset;
    }


    /**
     * Get uploaded at
     * @return int Uploaded date (UNIX timestamp)
     */
    public function getUploadedAt(): int {
        return $this->uploadedAt;
    }


    /**
     * @inheritdoc
     */
    public function getDigest(): string {
        return $this->key;
    }
}
