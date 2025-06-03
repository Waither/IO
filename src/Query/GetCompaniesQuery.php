<?php

namespace App\Query;

class GetCompaniesQuery {
    public string $query;
    public function __construct($query = '') {
        $this->query = $query;
    }
}