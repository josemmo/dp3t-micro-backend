<?php
namespace App\Model;

use App\Utils\DB;

class ExposeeList extends AbstractModel {
    private $exposees = [];
    private $latestTime = -1;
    private $latestDigests = null;

    /**
     * Create instance from onset
     * @param  string $onset Date in YYYY-MM-DD format
     * @return self          Instance
     */
    public static function fromOnset(string $onset): self {
        $instance = new self();

        $results = DB::getAll('SELECT `key`, onset, uploaded_at FROM exposees WHERE onset=?s', $onset);
        foreach ($results as $item) {
            $instance->addExposee(new Exposee($item['key'], $item['onset'], strtotime($item['uploaded_at'])));
        }

        return $instance;
    }


    /**
     * Add exposee to list
     * @param Exposee $exposee Exposee instance
     */
    private function addExposee(Exposee $exposee) {
        $this->exposees[] = $exposee;

        $uploadedAt = $exposee->getUploadedAt();
        if ($uploadedAt == $this->latestTime) {
            $this->latestDigests[] = $exposee->getDigest();
        } elseif ($uploadedAt > $this->latestTime) {
            $this->latestTime = $uploadedAt;
            $this->latestDigests = [$exposee->getDigest()];
        }
    }


    /**
     * Get list of exposees
     * @return Exposee[] List of exposees
     */
    public function getExposees(): array {
        return $this->exposees;
    }


    /**
     * @inheritdoc
     */
    public function getDigest(): string {
        $payload = ($this->latestDigests === null) ? "" : implode('', $this->latestDigests);
        return hash('md4', $payload, true);
    }
}
