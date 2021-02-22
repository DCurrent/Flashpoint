<?php 
	
	
	require(__DIR__.'/source/main.php');
	
	/* Page caching. */
	$page_obj = new \dc\Prudhoe\PageCache();

    /* 
    * Set up access control, then get and verify 
    * log in status. Needs fix now.
	*/
    $access_obj_process = new \dc\stoeckl\process();
	$access_obj_process->get_config()->set_authenticate_url(APPLICATION_SETTINGS::AUTHENTICATE_URL);
	$access_obj_process->get_config()->set_use_local(FALSE);
	$access_obj_process->process_control();
		
    $access_obj = new \dc\stoeckl\status();
	$access_obj->get_config()->set_authenticate_url(APPLICATION_SETTINGS::AUTHENTICATE_URL);	
	$access_obj->verify();

	/* Main navigaiton. */
	$obj_navigation_main = new Navigation();
	$obj_navigation_main->generate_markup_nav_public();
	$obj_navigation_main->generate_markup_footer();	
	
?>

<!DOCtype html>
<html lang="en">
    <head>
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo APPLICATION_SETTINGS::NAME; ?></title>        
        
         <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <link rel="stylesheet" href="source/css/style.css" />
        <link rel="stylesheet" href="source/css/print.css" media="print" />
    </head>
    
    <body>          
        <!-- Modal -->
        <div id="help_link_blue" class="modal fade" role="dialog">
          <div class="modal-dialog">
        
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Link Blue</h4>
              </div>
              <div class="modal-body">
                <p>Link Blue is the University of Kentucky's campus wide Active Directory login. It is the same account name and password you use to log into a workstation. <a href="//www.uky.edu/UKHome/subpages/linkblue.html" target="_blank">Click here</a> for more information.</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>        
          </div>
        </div>
    
        <div id="container" class="container">            
            <?php echo $obj_navigation_main->get_markup_nav(); ?>                                                                                
            <div class="page-header">
                <h1><?php echo APPLICATION_SETTINGS::NAME; ?></h1>
                <p>
				<?php
				
					echo '<!--account:'.$access_obj->get_account().'-->';
					// Logged in?
					if($access_obj->get_account())
					{
						/* This sets the $time variable to the current hour in the 24 hour clock format */
						$time = date("H");
						/* Set the $timezone variable to become the current timezone */
						$timezone = date("e");
						/* If the time is less than 1200 hours, show good morning */
						if ($time < "12") {
							echo "Good morning ";
						} else
						/* If the time is grater than or equal to 1200 hours, but less than 1700 hours, so good afternoon */
						if ($time >= "12" && $time < "17") {
							echo "Good afternoon ";
						} else
						/* Should the time be between or equal to 1700 and 1900 hours, show good evening */
						if ($time >= "17") {
							echo "Good evening ";
						}
						echo $access_obj->get_name_f();
				?>! Thank you for using <?php echo APPLICATION_SETTINGS::NAME; ?>, a Fire Marshal services system for UK Environmental Health And Safety. To get started, choose an item from the navigaton bar at top of the screen.</p>
                <?php
					}
					else
					{
				?>
                		<p>Welcome to <?php echo APPLICATION_SETTINGS::NAME; ?>, a Fire Marshal services system for UK Environmental Health And Safety. In order to use <?php echo APPLICATION_SETTINGS::NAME; ?>, please log in using your <a href="#" data-toggle="modal" data-target="#help_link_blue">Link Blue</a> account and password. Do not include the domain ("mc", "ad", or "@...") in your account name.</p>
            		
                    	<p><?php echo $access_obj->dialog(); ?></p>
                    	
                        <!--Note: PHP self is nessesary to override any link vars.-->
                        <form role="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group">
                                <label for="email">Account</label>
                                <input type="text" class="form-control" name="account" id="account" required>
                            </div>
                            <div class="form-group">
                                <label for="pwd">Password</label>
                                <input type="password" class="form-control" name="credential" id="credential" required>
                            </div>
                            
                            <button type="submit" name="access_action" value="<?php echo \dc\stoeckl\ACTION::LOGIN; ?>" class="btn btn-default"><span class="glyphicon glyphicon-log-in"></span> Login</button>
                        </form>
            
                <?php
					}
				?>
            </div> 
                    
            <?php echo $obj_navigation_main->get_markup_footer(); ?>
        </div><!--container-->        
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

</body>
</html>

<?php
	/* Collect and output page markup. */
	$page_obj->markup_and_flush();	
	$page_obj->output_markup();
?>