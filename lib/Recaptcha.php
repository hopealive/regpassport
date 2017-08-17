<?php
/**
 * Description of Recaptcha
 *
 * @author hopealive
 */
class Recaptcha
{
    public function validateRecaptcha()
    {
        $captcha = $_POST['g-recaptcha-response'];
        if(!$captcha){
            return false;
        }
        $secretKey = "6LeY4SwUAAAAAHv1pvwaAqeUIjLdxp9y2Efn18Xb";
        $ip = $_SERVER['REMOTE_ADDR'];
        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
        $responseKeys = json_decode($response,true);
        if(intval($responseKeys["success"]) !== 1) {
            //spammer
            return false;
        } else {
            return true;
        }
    }
}