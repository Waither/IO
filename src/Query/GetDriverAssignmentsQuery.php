<?php
namespace App\Query;

class GetDriverAssignmentsQuery {
    public function __construct(public string $driverId){}
}
