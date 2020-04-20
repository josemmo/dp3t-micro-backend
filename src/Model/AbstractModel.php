<?php
namespace App\Model;

abstract class AbstractModel {
    /**
     * Get digest
     * @return string Digest raw bytes
     */
    public abstract function getDigest(): string;
}
