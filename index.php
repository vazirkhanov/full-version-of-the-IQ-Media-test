<?php include 'header.php';?>

<?php include 'request.php';?> 

    <div class="container text-center">
        <div class="row" style="margin-top: 20%;">
   
        </div>
        <div class="row">
            <form action="" method="GET">
                <div class="input-group mb-3">
                    <?php $cut_link_value = isset($_GET['cut_link']) ? htmlspecialchars($_GET['cut_link']) : ''; ?>
<input type="text" value="<?= $cut_link_value ?>" name="cut_link" class="form-control" placeholder="Insert link" aria-describedby="button-addon2">

                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Execute</button>
                </div>
            </form>
        </div>
    </div>
<?php include 'footer.php';?>
