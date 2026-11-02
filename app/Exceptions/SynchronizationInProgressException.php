<?php

namespace App\Exceptions;

/**
 * Raised when a Steam synchronisation is attempted while another run for the
 * same member is already holding the per-user lock.
 */
class SynchronizationInProgressException extends \Exception {}
