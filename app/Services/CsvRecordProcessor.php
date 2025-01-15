<?php

namespace App\Services;

use App\Models\ImportedUser;
use Illuminate\Support\Facades\Log;
use Exception;

class CsvRecordProcessor
{
    /**
     * Przetwarza jeden rekord CSV.
     *
     * @param array $record
     * @param int $importId
     * @return bool True, jeśli rekord został poprawnie przetworzony.
     */
    public function processRecord(array $record, int $importId): bool
    {
        try {
            [$firstName, $lastName, $email] = array_map('trim', $record);

            if ($this->validateRecord($firstName, $lastName, $email)) {
                ImportedUser::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'import_id' => $importId,
                ]);
                return true;
            } else {
                $this->logInvalidRecord($importId, $firstName, $lastName, $email);
                return false;
            }
        } catch (Exception $e) {
            Log::error("Exception while processing record for import ID {$importId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Waliduje rekord CSV.
     */
    private function validateRecord(string $firstName, string $lastName, string $email): bool
    {
        return $this->validateName($firstName) && $this->validateName($lastName) && $this->validateEmail($email);
    }

    /**
     * Waliduje pole imienia/nazwiska.
     */
    private function validateName(string $name): bool
    {
        return !empty($name) && preg_match('/^[a-zA-Z\s]+$/', $name);
    }

    /**
     * Waliduje adres email.
     */
    private function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Loguje błędny rekord CSV.
     */
    private function logInvalidRecord(int $importId, string $firstName, string $lastName, string $email): void
    {
        Log::warning("Invalid record in import ID {$importId}: FirstName='{$firstName}', LastName='{$lastName}', Email='{$email}'");
    }
}
