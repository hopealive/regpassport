<div class="row featurette" id="block-signup">
    <div class="col-md-9 push-md-3">
        <h2 class="featurette-heading">Форма реєстрації</h2>

        <?php
        $signupHidden = "";
        if (isset($_POST['action']) && $_POST['action'] == 'signup') {
            if (empty($signupErrorMessage)) {
                $signupHidden = "hidden";
                echo '<div class="alert alert-success" role="alert">Ви успішно зареєструвались</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">'.$signupErrorMessage.'</div>';
            }
        }
        ?>
        <form action="/#block-signup" class="form-signup <?php echo $signupHidden; ?>" method="POST">
            <input type="text" name="firstname" class="form-control" placeholder="Ім'я" required>
            <br>
            <input type="text" name="patronymic" class="form-control" placeholder="По-батькові" required >
            <br>
            <input type="text" name="surname" class="form-control" placeholder="Прізвище" required >
            <br>
            <div id="signup-recaptcha-widget"></div>
            <br>
            <input type="hidden" name="action" id="action" value="signup">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Зареєструватись</button>
        </form>
    </div>
</div>
<hr class="featurette-divider">
