<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="robots" content="noindex,noarchive">
	<meta name="generator" content="LazyCMS">
	<title>Internal error</title>

	<link href="<?php echo $this->resourcesPath; ?>debug.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo $this->resourcesPath; ?>debug.js" type="text/javascript"></script>
</head>

<body id="infoScreen">
	<!-- SCREEN CONTENT -->
	<div id="content">
		<?php $this->printContent() ?>
	</div>

	<!-- CALL STACK -->
	<fieldset>
		<legend onclick="snippy_toggleBlock('stackTrace')" class="button">Call stack</legend>
		<div id="stackTrace">
			<?php echo $this->formater->formatTrace( $this->trace ) ?>
		</div>
	</fieldset>

	<!-- ENVIRONMENT -->
	<fieldset>
		<legend onclick="snippy_toggleBlock('environment')" class="button">Environment</legend>
		<div class="block" id="environment">
			<h3 onclick="snippy_toggleBlock('session')" class="button">$_SESSION</h3>
			<div id="session" class="collapsed">
				<?php echo $this->formater->formatListVertical( $_SESSION ); ?>
			</div>

			<h3 onclick="snippy_toggleBlock('server')" class="button">$_SERVER</h3>
			<div id="server" class="collapsed">
				<?php echo $this->formater->formatListVertical( $_SERVER ); ?>
			</div>

			<h3 onclick="snippy_toggleBlock('settings')" class="button">Settings</h3>
			<div id="settings" class="collapsed">
				<?php
					$constants = get_defined_constants( true );

					echo $this->formater->formatListVertical( $constants['user'] ?: array() );
				?>
			</div>
		</div>
	</fieldset>

	<!-- HTTP REQUEST -->
	<fieldset>
		<legend onclick="snippy_toggleBlock('httpRequest')" class="button">HTTP request</legend>
		<div class="block" id="httpRequest">
			<h3 onclick="snippy_toggleBlock('reqHeaders')" class="button">Headers</h3>
			<div id="reqHeaders" class="collapsed">
				<?php
					$headersArr = $_SERVER;
					$headers = array();
					foreach( $_SERVER as $k => $v ) {
						if( substr( $k, 0, 5 ) != 'HTTP_' )
						continue;

						$name = substr( $k, 5 );
						$name = str_replace( '_', ' ', $name );
						$name = ucwords( strtolower( $name ) );
						$name = str_replace( ' ', '-', $name );

						$headers[$name] = $v;
					}

					echo $this->formater->formatListVertical( $headers );
				?>
			</div>

			<h3 onclick="snippy_toggleBlock('get')" class="button">$_GET</h3>
			<div id="get" class="collapsed">
				<?php echo $this->formater->formatListVertical( $_GET ); ?>
			</div>

			<h3 onclick="snippy_toggleBlock('post')" class="button">$_POST</h3>
			<div id="post" class="collapsed">
				<?php echo $this->formater->formatListVertical( $_POST ); ?>
			</div>

			<h3 onclick="snippy_toggleBlock('cookie')" class="button">$_COOKIE</h3>
			<div id="cookie" class="collapsed">
				<?php echo $this->formater->formatListVertical( $_COOKIE ); ?>
			</div>
		</div>
	</fieldset>

	<!-- HTTP RESPONSE -->
	<fieldset>
		<legend onclick="snippy_toggleBlock('httpResponse')" class="button">HTTP response</legend>
		<div class="block" id="httpResponse">
			<h3 onclick="snippy_toggleBlock('respHeaders')" class="button">Headers</h3>
			<div id="respHeaders" class="collapsed">
				<?php
					$headersArr = headers_list();
					$headers = array();
					foreach( $headersArr as $h ) {
						$delim = strpos( $h, ':' );
						$headers[ substr($h, 0, $delim) ] = trim( substr($h, $delim+1) );
					}

					echo $this->formater->formatListVertical( $headers );
				?>
			</div>
		</div>
	</fieldset>

</body>