<?php

namespace App\helpers;

use App\core\Helper;
use InvalidArgumentException;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use App\exceptions\validation\InvalidValidationRuleException;

class ValidationHelper extends Helper
{
    protected array $rules = ["required", "username", "email", "password", "phone_number", "url"];

    /**
     * checks whether the rule is valid
     *
     * @param string $rule
     * @return bool
     */
    public function checkIfRuleIsValid(string $rule): bool
    {
        return in_array(strtolower($rule), $this->rules);
    }

    final public function checkPasswordStrength($password)
    {
        // $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
        // !$uppercase ||
        if (!$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            return false;
        }
        return true;
    }

    /**
     * Check whether username contains invalid characters
     *
     * @param string $username
     * @return boolean
     */
    final public function isUsernameValid(string $username): bool
    {
        // Check for invalid characters
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return false;
        }
        // Check for length
        if (strlen($username) < 4 || strlen($username) > 20) {
            return false;
        }
        return true;
    }

    /**
     * Checks whether an array or string is empty and returns boolean if not
     *
     * @param string|array $value
     * @return string|bool
     */
    public function isEmpty(string|array $value): string|bool
    {
        return empty($value);
    }

    /**
     * Check For Empty error fields
     *
     * @param mixed $value
     * @param string $customErrorMsg
     * @return InvalidArgumentException|string|boolean
     */
    public function isEmptyField(mixed $value, $customErrorMsg = ""): InvalidArgumentException | string |bool
    {
        if (is_iterable($value)) {
            throw new InvalidArgumentException("Arrays and other iterables are not expected");
        }

        $errorMessage = function (string $errorMsg)  use ($customErrorMsg) {
            return strlen($customErrorMsg) ? $customErrorMsg : $errorMsg;
        };


        if ($this->isEmpty($value)) {
            return $errorMessage("This field is required");
        }

        return false;
    }

    /**
     * Validates data depending on the rules used
     *
     * @param string $rule
     * @param mixed $data
     * @param string $message
     * @return string|InvalidValidationRuleException|InvalidArgumentException
     */
    public function makeValidations(string $rule, mixed $value, string $customErrorMsg = "", bool $checkEmpty = false): string|InvalidValidationRuleException|InvalidArgumentException
    {
        if (!$this->checkIfRuleIsValid($rule)) {
            throw new InvalidValidationRuleException($rule);
        } elseif (is_iterable($value)) {
            throw new InvalidArgumentException("Arrays and other iterables are not expected");
        }

        $errorMessage = function (string $errorMsg)  use ($customErrorMsg) {
            return strlen($customErrorMsg) ? $customErrorMsg : $errorMsg;
        };

        # When $checkEmpty is set
        if ($checkEmpty) {
            if ($this->isEmpty($value)) {
                return "This field is required";
            }
        }

        return match (strtolower($rule)) {
            "username" => !$this->isUsernameValid($value) ? $errorMessage("Username contains invalid characters") : false,
            "email" => !$this->validateEmail($value) ? $errorMessage("Invalid email") : false,
            "password" => !$this->checkPasswordStrength($value) ? $errorMessage("Your password is weak, should contain numbers, special Characters and should be 8 characters long") : false,
            "phone_number" => !$this->validatePhoneNumber($value) ? $errorMessage("Invalid phone number is entered") : false,
            "url" => !filter_var($value, FILTER_VALIDATE_URL) ? $errorMessage("Invalid URL entered.") : false
        };
    }


    /**
     * Validates email
     *
     * @param string $email
     * @return boolean
     */
    public function validateEmail($email): bool
    {
        return filter_var($this->filter($email), FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validates phone number
     *
     * @param mixed $phone_number
     * @return bool
     */
    public function validatePhoneNumber($phone_number, $country = "UG"): bool
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $inputPhoneNumber = $phone_number;
        # Checking if the phone number is valid
        $phoneNumber = $phoneNumberUtil->parse($inputPhoneNumber, $country);
        $isValid = $phoneNumberUtil->isValidNumber($phoneNumber);
        # Will Return the result which will be a bool
        return $isValid;
    }
}
