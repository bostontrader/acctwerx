<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcctWerx</title>

    <? // If you are using the CSS version, only link these 2 files, you may add app.css to use for your overrides if you like ?>
    <?= $this->Html->css('normalize.css'); ?>
    <?= $this->Html->css('foundation.css'); ?>
    <!--
    <script src="js/vendor/modernizr.js"></script> -->

</head>
<body>

    <?php //if($currentUser) {
        //$userMsg   = "current user = " . $currentUser['username'];
        $userMsg='';
        //$loginLink = $this->Html->link(
            //'Logout',
            //'/users/logout',
            //['class' => 'button']
        //);
        $loginLink='';

    //} else {
        //$userMsg   = "not logged in";
        //$loginLink = $this->Html->link(
            //__('Login'),
            //'/users/login',
            //['class' => 'button']
        //);
    //}
    ?>


    <nav class="top-bar" data-topbar role="navigation">
        <ul class="title-area">
            <li class="name">
                <h1><a href="#">AcctWerx</a></h1>
            </li>
        </ul>

        <section class="top-bar-section">
            <!-- Right Nav Section -->
            <ul class="right">
                <li><a href="#"><?= $userMsg ?></a></li>
                <li><a href="#"><?= $loginLink ?></a></li>

            </ul>

        </section>
    </nav>

    <?php
        //if($currentUser) {
        //$crumb=[' > ','Home'];
    //$n1=$this->Html->getCrumbs();
    //$n2=$this->Html->getCrumbList();
        // The content view has already been evaluated and HtmlHelper has
        // an array of links ready to become breadcrumbs.  Finally, prepend this
        // and display the full breadcrumb trail in the layout.
        echo $this->Html->getCrumbs(' > ', 'Home');
    //$n1=$this->Html->getCrumbs();
    //$n2=$this->Html->getCrumbList();
        // start session, level = 0
        echo $this->fetch('content');
        //}
    ?>

</body>
</html>
