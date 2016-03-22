<html>
<body>
	<h1>Search Page</h1>
	<p>Search Conditions:</p>
	<div id="search_panel">
		<form action="" method="post">
			<fieldset>
				Key Words: <input type="text" name="description"> <br />
				<br/> Date Range: <br>
				From: <input id="from" type="date" value=""><br/>
				To:   <input id="to" type="date" value=""><br/>
				Result rank by: <select name="type" Method="">Rank Method</option>
				<?php
				   echo "<option value=>Default</option>";
					echo "<option value=f>most-recent-first</option>"; 
					echo "<option value=l>most-recent-last</option>"; 
				?>
				</select><br />
				<button type="reset">Reset</button>
				<input type="submit" name="submit_search" value="Search">
			</fieldset>
		</form>
	</div>
	
</body>

</html>