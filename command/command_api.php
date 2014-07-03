<?php global $path, $session; ?>

<h2>Command API</h2>

<h3>Apikey authentication</h3>
<p>If you want to call any of the following action's when your not logged in, add your read & write apikey to the URL of your request: &apikey=APIKEY.</p>

<p><b>Read & Write:</b><br>
<input type="text" style="width:230px" readonly="readonly" value="<?php echo get_apikey_write($session['userid']); ?>" />
</p>

<h3>Get command</h3>
<p><a href="<?php echo $path; ?>command/get.json"><?php echo $path; ?>command/get.json</a></p>

<h3>Insert command</h3>
<p><a href="<?php echo $path; ?>command/insert?cmd=turnonheating"><?php echo $path; ?>command/insert?cmd=turnonheating</a></p>

<h3>Command list</h3>
<p><a href="<?php echo $path; ?>command"><?php echo $path; ?>command</a></p>
