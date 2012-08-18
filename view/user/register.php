<?php debug(array($_SESSION,$viewData));?>
<div class="well">
	<h2>You Won't Read This Anyway</h2>
	<form action="/user/create" id="userLoginForm" class="form-horizontal" method="post" accept-charset="utf-8">
		<fieldset>
			<div class="control-group required">
				<label for="userEmail" class="control-label">E-mail</label>
				<div class="controls">
					<input name="userEmail" id="userEmail" class="input-xlarge placeholder" required="required" title="Enter your Email Address" placeholder="Enter your Email Address" type="email" value="<?php echo isset($_POST['userEmail']) ? $_POST['userEmail'] : ''; ?>">
				</div>
			</div>
			<div class="control-group required">
				<label for="userPassword" class="control-label">Password</label>
				<div class="controls">
					<input name="userPassword" id="userPassword" class="input-xlarge" required="required" type="password" placeholder="Enter a password">
				</div>
			</div>
			<div class="form-actions">
				<div id="passwordRecovery">
					<a href="/user/forgotpassword" class="btn">Forgot your password? </a>
				</div>
				<div class="submit" style="margin-top:5px;">
					<input class="btn btn-primary" type="button" value="Register" onclick="$.ajax({ url: '/user/create', type: 'post', dataType: 'json', data: { email: document.getElementById('userEmail').value, password: document.getElementById('userPassword').value }, success: function(data) { if (data.error)  { alert(data.error); } else { window.location.reload(); } } })">
				</div>
			</div>
		</fieldset>
	</form>
</div>
