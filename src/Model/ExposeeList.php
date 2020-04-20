<?php
namespace App\Model;

use App\Utils\DB;

class ExposeeList extends AbstractModel {
    private $exposees = [];

    /**
     * Create instance from onset
     * @param  string $onset Date in YYYY-MM-DD format
     * @return self          Instance
     */
    public static function fromOnset(string $onset): self {
        $instance = new self();

        $results = DB::getAll('SELECT `key`, onset FROM exposees WHERE onset=?s', $onset);
        foreach ($results as $item) {
            $instance->addExposee(new Exposee($item['key'], $item['onset']));
        }

        return $instance;
    }


    /**
     * Add exposee to list
     * @param Exposee $exposee Exposee instance
     */
    private function addExposee(Exposee $exposee) {
        $this->exposees[$exposee->getDigest()] = $exposee;
    }


    /**
     * Get list of exposees
     * @return Exposee[] List of exposees
     */
    public function getExposees(): array {
        return array_values($this->exposees);
    }


    /**
     * @inheritdoc
     */
    public function getDigest(): string {
        $digests = array_keys($this->exposees);
        sort($digests);
        return hash('md5', implode('', $digests), true);
    }
}
