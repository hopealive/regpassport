<?php
/**
 * Description of Validators
 *
 * @author hopealive
 */
class Validators
{

    public function validateAutocomplete($item)
    {
        if (!preg_match("/[0-9A-Za-zА-Яа-яЁёЇїІіЄєҐґ]/", $item)) {
            return FALSE;
        }
        return true;
    }

    public function validateUser($row)
    {
        foreach ($row as $key => $r) {
            if (!preg_match("/[A-Za-zА-Яа-яЁёЇїІіЄєҐґ]/", $r)) {
                return FALSE;
            }
        }
        return true;
    }
    
}