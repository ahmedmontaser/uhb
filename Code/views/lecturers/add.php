<?php
$page = "lecturers";
require_once"../../init/init.php";

if ( !isAdmin() ) {
	redirect("public");
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	/** Validating the SSN */
	if( $_POST['ssn'] != "" ) {
		$ssn = filter_var($_POST['ssn'], FILTER_SANITIZE_STRING);

		if ( isExistIn($ssn, "lecturers", "SSN") ) {
			$_SESSION['msg'][] = "The SSN Must Be Unique";
		}
	} else {
		$_SESSION['msg'][] = "You Must Enter The SSN";
	}


	/** Validating the Name */
	if( $_POST['name'] != "" ) {
		$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	} else {
		$_SESSION['msg'][] = "You Must Enter The Name";
	}

	/** Validating the Email */
	if( $_POST['email'] != "" ) {
		$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

		if ( isExistIn($email, "lecturers", "email") ) {
			$_SESSION['msg'][] = "The Email Must Be Unique";
		}
	} else {
		$_SESSION['msg'][] = "You Must Enter The Email";
	}

	/** Validating the Password */
	if( $_POST['password'] != "" ) {
		$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
	} else {
		$_SESSION['msg'][] = "You Must Enter The Password";
	}

	/** Validating the Department */
	if ( $_POST['department'] != 0 ) {
		$department = $_POST['department'];
	} else {
		$_SESSION['msg'][] = "You Must Select a Department";
	}

	/** Validating the Phone */
	if( $_POST['phone'] != "" ) {
		$phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);

		if ( isExistIn($phone, "lecturers", "phone") ) {
			$_SESSION['msg'][] = "The SSN Must Be Unique";
		}
	} else {
		$_SESSION['msg'][] = "You Must Enter The Phone";
	}

	$cv = $_FILES['cv'];

	$cvName	= rand(1000000, 9999999) . $cv['name'];
	$cvTmp = $cv['tmp_name'];

	/** Validating the SSN */
	if( !empty( $_FILES['cv']['name'] ) ) {
		move_uploaded_file( $cvTmp, "..\..\uploads\cvs\\" . $cvName );
	} else {
		$_SESSION['msg'][] = "You Must Upload The CV";
	}

	$gender = $_POST['gender'];

	if ( empty( $_SESSION['msg'] ) ) {
		$salt = substr( md5( rand() ), 0 , 10);
		$password = sha1( $password . $salt );
		$stmt = $con->prepare("INSERT INTO lecturers(SSN, lec_name, email, password, salt, phone, cv, department_id, gender) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute([$ssn, $name, $email, $password, $salt, $phone, $cvName, $department, $gender]);

		redirect("lecturers");
	}
}

getErrors();

$stmt = $con->prepare("SELECT * FROM colleges");
$stmt->execute();

$colleges = $stmt->fetchAll();

?>

	<div class="container py-5">
		<h3>Add Lecturer</h3>
		<hr/>

		<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for="ssn">SSN</label>
				<input type="text" class="form-control" id="ssn" name="ssn" required value="<?= $_POST['ssn'] ?? "" ?>">
			</div>

			<div class="form-group">
				<label for="name">Name</label>
				<input type="text" class="form-control" id="name" name="name" required value="<?= $_POST['name'] ?? "" ?>">
			</div>

			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" class="form-control" id="password" name="password" >
			</div>

			<div class="form-group">
				<label for="college">College</label>
				<select class="form-control" id="college">
					<option value="0">...</option>
					<?php foreach ($colleges as $college): ?>
						<option value="<?= $college['id'] ?>"><?= $college['colg_name'] ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="form-group">
				<label for="department">Department</label>
				<select class="form-control" id="department" name="department">
					<option value="0">...</option>
				</select>
			</div>

			<div class="form-group">
				<label for="email">Email</label>
				<input type="email" class="form-control" id="email" name="email" value="<?= $_POST['email'] ?? "" ?>">
			</div>

			<div class="form-group">
				<label for="phone">Phone</label>
				<input type="text" class="form-control" id="phone" name="phone" value="<?= $_POST['phone'] ?? "" ?>">
			</div>

			<div class="form-group">
				<label for="gender">Gender</label>
				<select class="form-control" id="gender" name="gender">
					<option value="0">Male</option>
					<option value="1">Female</option>
				</select>
			</div>

			<div class="form-group">
				<label for="cv">CV</label>
				<input type="file" class="filestyle" id="cv" name="cv" required>
			</div>

			<button type="submit" class="btn btn-primary">Save</button>
			<a href="index.php" class="btn btn-secondary ml-3">Close</a>
		</form>
	</div>

<?php require_once"../includes/footer.php"; ?>