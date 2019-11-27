<!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8">
	<title>All users</title>
	<href link="/presentation/style.css" rel="stylesheet">
	 <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    </style>
	</head>
	<body>
		<h1> All users </h1>
		
		<?php
		
			/* initialisation des différentes variables du PDO */
			$host = 'localhost';
			$port = '3306';
			$db   = 'my_activities';
			$user = 'root';
			$pass = 'root';
			$charset = 'utf8';
			
			/* définition du PDO */
			$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
			
			$options = [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES   => false,
			];
			
			try {
				/* création de l'accès à la BD */
				$pdo = new PDO($dsn, $user, $pass, $options);
			} catch (PDOException $e) {
				throw new PDOException($e->getMessage(), (int)$e->getCode());
			}
		?>
		<form action="all_users.php" method="GET">
			<div>Start with letter:
				<input type="text" id="premiereLettre" name="premiereLettre" size="10">
				and status is:
				<select id="status" name="status">
					<option value="2"<?php if ($_GET['status'] == 2) echo ' selected' ?>>Active Account</option>
					<option value="1"<?php if ($_GET['status'] == 1) echo ' selected' ?>>Waiting for account validation</option>
					<option value="3"<?php if ($_GET['status'] == 3) echo ' selected' ?>>Waiting for account deletion</option>
				</select>
				<input type="submit" value="OK">
			</div>
		</form>
		
		<?php
		
			$start_letter = "";
			$status_id = 2;
			
			if (ISSET($_GET['status']) && $_GET['status'] == 1) {			
				$status_id = 1;
			} else if (ISSET($_GET['status']) && $_GET['status'] == 3) {
				$status_id = 3;
			}
			
			if (ISSET($_GET['premiereLettre'])) {
				$start_letter = $_GET['premiereLettre'];
				$start_letter = $start_letter."%";
			}
		
			$stmt = $pdo->prepare("SELECT users.id as user_id, username, email, s.name FROM users JOIN status s ON users.status_id = s.id 
					WHERE username LIKE :start_letter AND s.id = :status_id ORDER BY username ASC");
			$stmt->execute(['start_letter' => $start_letter, 'status_id' => $status_id]);

					
		?>
		<table> 
			<tr class="entete"> 
				<th>Id</th> 
				<th>Username</th> 
				<th>Email</th> 
				<th>Status</th> 
			</tr>
		<?php
			while ($row = $stmt->fetch()) {
				echo "<tr> <td>" . $row['user_id'] . "</td><td>" .$row['username'] . "</td><td>" . $row['email'] . "</td><td>" . $row['name'] . "</td>";
				if ($status_id != 3) {
					echo "<td><a href=\"all_users.php?premiereLettre=&status=3&action=askDeletion\">Ask deletion</a></td>";
				}
				echo "</tr>";
			}
		?>
		</table>
	</body>
</html>