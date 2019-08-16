<?php
/** FILE INFO
 * This WEB PAGE is the GRAPHICAL USER INTERFACE for the algorithms I made for Richard.
 *
 * Created by PhpStorm.
 * User: julius
 * Date: 8/28/2018
 * Time: 4:14 PM
 */

echo "<h1 class='text-center'> &nbsp;Richard Charlow tools</h1> <br>";

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Richard Charlow</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <style>
        /* Sticky footer styles
 -------------------------------------------------- */
        html {
            position: relative;
            min-height: 100%;
        }

        body {
            margin-bottom: 60px; /* Margin bottom by footer height */
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 60px; /* Set the fixed height of the footer here */
            line-height: 60px; /* Vertically center the text there */
            background-color: #f5f5f5;
        }

        /* Custom page CSS
        -------------------------------------------------- */
        /* Not required for template or sticky footer method.
         .container {
            width: auto;
            max-width: 680px;
            padding: 0 15px;
        }
         */
    </style>
</head>

<body>

<div class="container">
    <h3>Loan Officer Delegate</h3>
    
    <form action="loanOfficer.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="loan_officers_info">Upload Loan Officers info</label>
            <input type="file" name="loan_officers[]" id="loan_officers_info" class="form-control-file"
                   aria-describedby="emailHelp" placeholder="Loan Officers">
        </div>

        <div class="form-group">
            <label for="loan_officers_data">Upload Data</label>
            <input type="file" name="loan_officers[]" id="loan_officers_data" class="form-control-file"
                   aria-describedby="emailHelp" placeholder="Loan Officers">
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Loan Officer Delegate</button>
    </form>
</div><!-- END OF div.container -->

<hr>

<div class="container">
    <h3>Right Shift Up Group</h3>
    <form action="rightShiftUpGroup.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="right_shift_up">Upload Data</label>
            <input type="file" name="loan_officers[]" id="right_shift_up" class="form-control-file"
                   aria-describedby="emailHelp" placeholder="Loan Officers">
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Right Shift Up Group</button>
    </form>
</div>

<hr>

<div class="container">
    <h3>Suppression List</h3>
    <form action="suppressionList.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="suppression_list">Upload Suppression List</label>
            <input type="file" name="suppression_list_files[]" id="suppression_list" class="form-control-file"
                   aria-describedby="emailHelp" placeholder="Loan Officers">
        </div>

        <div class="form-group">
            <label for="suppression_data">Upload Data</label>
            <input type="file" name="loan_officers[]" id="suppression_data" class="form-control-file"
                   aria-describedby="emailHelp" placeholder="Loan Officers">
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Suppression List</button>
    </form>
</div>

<br>

<footer class="footer">
    <div class="container">
        <h1 class="text-center">version 0.0.1</h1>
    </div>
</footer>

</body>

</html>