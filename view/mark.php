<div class="row featurette" id="block-search">

    <div class="col-md-9 push-md-3">
        <h2 class="featurette-heading">Відмітитись</h2>

        <?php
        $markHidden = "";
        if ( $viewParams['action'] == "mark" ){
            $markAlertClass = "danger";
            if ( $viewParams['status'] == "ok" ){
                $markHidden = "hidden";
                $markAlertClass = "success";
            }
            echo '<div class="alert alert-'.$markAlertClass.'" role="alert">'.$viewParams['message'].'</div>';
        }

        ?>

        <form action="/#block-search" class="form-search <?php echo $markHidden; ?>" method="POST">
            <div class="ui-widget">
                <input type="text" name="search-input" id="search-input" class="form-control" placeholder="Введіть прізвище та ім'я або номер у списку" required>
                <input type="hidden" name="user-id" id="user-id">
                <input type="hidden" name="action" id="action" value="mark">
            </div>
            <p>&nbsp;</p>

            <div id="search-recaptcha-widget"></div>
            <br>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Відмітитись</button>
        </form>
    </div>
</div>
<hr class="featurette-divider">
