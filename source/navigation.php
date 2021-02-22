<?php

	require_once(__DIR__.'/main.php');

	class Navigation
	{
		const DIRECTORY_PRIME = '/apps/flashpoint';
		
		private
			$access_obj			= NULL,
			$directory_local	= NULL,
			$directory_prime	= NULL,
			$markup_nav			= NULL,
			$markup_footer		= NULL;
		
		public function __construct()
		{
			$this->directory_prime 	= self::DIRECTORY_PRIME;
			$this->access_obj		= new \dc\stoeckl\status();
			
			$this->access_obj->get_config()->set_authenticate_url(APPLICATION_SETTINGS::AUTHENTICATE_URL);
			
		}
		
		public function get_directory_local()
		{
			return $this->directory_local;
		}
		
		public function get_directory_prime()
		{
			return $this->get_directory_prime();
		}
		
		public function set_directory_local($value)
		{
			$this->directory_local = $value;
		}
		
		public function set_directory_prime($value)
		{
			$this->directory_prime = $value;
		}
		
		public function get_markup_footer()
		{
			return $this->markup_footer;
		}
		
		public function get_markup_nav()
		{
			return $this->markup_nav;
		}
			
		public function generate_markup_nav()
		{
			$class_add = NULL;
			
			if(!$this->access_obj->get_account()) $class_add .= "disabled";
			
			// Start output caching.
			ob_start();
		?>
        	
        
            <nav class="navbar">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#nav_main">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>                        
                        </button>
                        <a class="navbar-brand" href="<?php echo $this->directory_prime; ?>"><?php echo APPLICATION_SETTINGS::NAME; ?></a>
                    </div>
                    <div class="collapse navbar-collapse" id="nav_main">
                        <ul class="nav navbar-nav">
                            <!--<li class="active"><a href="#">Home</a></li>-->
                            <li class="dropdown"><a class="dropdown-toggle <?php echo $class_add; ?>" data-toggle="dropdown" href="#">Incidents</a>                            	<ul class="dropdown-menu">
                            		<li><a class=" <?php echo $class_add; ?>" href="<?php echo $this->directory_prime; ?>/alarm_list.php"><span class="glyphicon glyphicon glyphicon-list"></span> Alarm List</a>
                                    <li><a class=" <?php echo $class_add; ?>" href="<?php echo $this->directory_prime; ?>/alarm.php"><span class="glyphicon glyphicon-eye-open"></span> Alarm Detail View</a></li>
                                    <li><a class=" <?php echo $class_add; ?>" href="<?php echo $this->directory_prime; ?>/incident_log.php?id=<?php echo DB_DEFAULTS::NEW_ID ?>"><span class="glyphicon glyphicon glyphicon-list"></span> Public Fire Log</a></li>
                                    
                                    <li><a class=" <?php echo $class_add; ?>" href="<?php echo $this->directory_prime; ?>/alarm_entry.php?id=<?php echo DB_DEFAULTS::NEW_ID ?>"><span class="glyphicon glyphicon-plus"></span> Alarm Entry (for customers)</a></li>
                            	</ul>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                        <?php
							if($this->access_obj->get_account())
							{
						?>
                   		  <li><a href="<?php echo $this->access_obj->get_config()->get_authenticate_url(); ?>?access_action=<?php echo \dc\stoeckl\ACTION::LOGOFF; ?>"><span class="glyphicon glyphicon-log-out"></span> <?php echo $this->access_obj->name_full(); ?></a></li>
                        <?php
							}
							else
							{
						?>
                        		<li><a href="<?php echo $this->access_obj->get_config()->get_authenticate_url(); ?>"><span class="glyphicon glyphicon-log-in"></span> Guest</a></li>
                        <?php
							}
						?>                   
                        </ul>
                    </div>
                </div>
            </nav>        	
        <?php
			
			// Collect contents from cache and then clean it.
			$this->markup_nav = ob_get_contents();
			ob_end_clean();	
			
			return $this->markup_nav;
		}	
		
		public function generate_markup_nav_public()
		{
			$class_add = NULL;
			
			if(!$this->access_obj->get_account()) $class_add .= "disabled";
			
			// Start output caching.
			ob_start();
		?>

            <nav class="navbar navbar-expand-lg navbar-light bg-light">
				<a class="navbar-brand" href="<?php echo $this->directory_prime; ?>"><?php //echo APPLICATION_SETTINGS::NAME; ?>University of Kentucky Fire Marshal</a>
				
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav mr-auto">
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Fire &amp; Drill Reports
							</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdown">
								<!--<a class="dropdown-item" href="#">Select an action</a>-->
								<!--<div class="dropdown-divider"></div>-->
								<a class="dropdown-item" href="<?php echo $this->directory_prime; ?>/alarm_list.php">Alarm Report List</a>
                                <a class="dropdown-item" href="<?php echo $this->directory_prime; ?>/alarm_entry.php">Create Fire Alarm Report</a>
							</div>
						</li>
					</ul>
					<div class="float-right">

						<?php
							if($this->access_obj->get_account())
							{
						?>
								<a href="<?php echo $this->access_obj->get_config()->get_authenticate_url(); ?>?access_action=<?php echo \dc\stoeckl\ACTION::LOGOFF; ?>"><span class="glyphicon glyphicon-log-out"></span> <?php echo $this->access_obj->name_full(); ?></a>
						<?php
							}
							else
							{
						?>
								<a href="<?php echo $this->access_obj->get_config()->get_authenticate_url(); ?>"><span class="glyphicon glyphicon-log-in"></span> Guest</a>
						<?php
							}
						?>
					</div>
				</div>				
			</nav>                  	
        <?php
			
			// Collect contents from cache and then clean it.
			$this->markup_nav = ob_get_contents();
			ob_end_clean();	
			
			return $this->markup_nav;
		}		
		
		public function generate_markup_footer()
		{
			ob_start();
			?>
			
			<br><br>
			<div class="card bg-light">
				<div class="card-body">
					
					<img class="float-right d-none d-sm-inline" src="<?php echo $this->directory_prime; ?>/media/php_logo_1.png" class="img-responsive pull-right .d-sm-none" alt="Powered by objected oriented PHP." title="Powered by object oriented PHP." />
					
					<img class="float-left d-none d-sm-inline" style="margin-right: 15px; width: 100px" src="<?php echo $this->directory_prime; ?>/media/uk_logo_1.png" class="img-responsive pull-right .d-sm-none" alt="Powered by objected oriented PHP." title="Powered by object oriented PHP." />
					
					<span class="text-muted small"><?php echo APPLICATION_SETTINGS::NAME; ?> Ver <?php echo APPLICATION_SETTINGS::VERSION; ?></span>
					<br>
					<span class="text-muted small">Developed by: <a href="mailto:dvcask2@uky.edu"><span class="glyphicon glyphicon-envelope"></span> Caskey, Damon V.</a></span>
					<br>
					<span class="text-muted small">Copyright &copy; <?php echo date("Y"); ?>, University of Kentucky.</span>
					<br>
				</div>
			</div>
					

			
			<?php
			// Collect contents from cache and then clean it.
			$this->markup_footer = ob_get_contents();
			ob_end_clean();
			
			return $this->markup_footer;
		}
	}

?>