<?php

	require __DIR__ . '/../config/config.php';
	/*
		a function to return all user records from the
		database, sorted by @created_at ASC
	*/
	function allUsers($request, $response) {
		$sql =  "SELECT * FROM users";
		try {
			$stmt = getConnection()->query($sql);
			$users = $stmt->fetchAll(PDO::FETCH_OBJ);
			$db = null;

			if (count($users) > 0) {
				$data = array( 
					'data' => $users, 
					'total' => count($users), 
					'status' => true 
				);
				return $response->withJson($data);
			} else {
				$data = array( 
					'data' => null, 
					'total' => count($users), 
					'status' => false 
				);
				return $response->withJson($data);
			}
		} catch (PDOException $exception) {
			$error = array( 'message' => $exception->getMessage());
	      return $response->withJson($error);
		}
	}
	/*
		a function to return a single user record from the
		database by @user_id
	*/
	function userById($request, $response) {
		$id = $request->getAttribute('id');
		$sql =  "SELECT * FROM users WHERE id = $id";
		try {
			$stmt = openConnection()->query($sql);
			$user = $stmt->fetchAll(PDO::FETCH_OBJ);
			$db = null;
			return $response->withJson($user);
		} catch (PDOException $exception) {
			$error = array( 'message' => $exception->getMessage());
	      return $response->withJson($error);
		}
	}
	/*
		a function to find a user record from the
		database, by @email
	*/
	function userByEmail($request, $response) {
		$email = $request->getAttribute('email');
		$sql =  "SELECT * FROM users WHERE email = '$email'";
		try {
			$stmt = openConnection()->query($sql);
			$user = $stmt->fetchAll(PDO::FETCH_OBJ);
			$db = null;
			return $response->withJson($user);
		} catch (PDOException $exception) {
			$error = array( 'message' => $exception->getMessage());
	      return $response->withJson($error);
		}
	}
	/*
		a function to create a user record in the
		database, @token, @firstname, @lastname, @email, @mobile, @password
		returns a user object.
	*/
	function createUser($request, $response) {

	   $user = $request->getParsedBody();
	   $sql = "INSERT INTO users (token, firstname, lastname, email, mobile, password ) 
	   VALUES (:token, :firstname, :lastname, :email, :mobile, :password)";

     	try {
     		if(!validateEmail($user['email'], $user['mobile'])) {

	   		$database = openConnection();
	   		// hashing the password
	   		$password = password_hash($user['password'], PASSWORD_DEFAULT);
     			$stmt = $database->prepare($sql);
		      $stmt->bindParam('token', $user['token']);
		      $stmt->bindParam('firstname', $user['firstname']);
		      $stmt->bindParam('lastname', $user['lastname']);
		      $stmt->bindParam('email', $user['email']);
		      $stmt->bindParam('mobile', $user['mobile']);
		      $stmt->bindParam('password', $password);
		      $stmt->execute();

		      $user_id = $user['id'] = $database->lastInsertId();
		      $database = null;
		      $message = array(
		      	'status' => 'success',
		     		'message' => "Congratulations, account no. $user_id was created successfully"
		      );
		      return $response->withJson($message);
     		} else {
     			$message = array(
		     		'status' => 'available',
		     		'message' => $user['email']." or ".$user['mobile']." already exists in our system."
		     	);
		     	return $response->withJson($message);
     		}
	   } catch(PDOException $exception) {
	      $message = array(
	      	'status' => 'error',
     			'message' => $exception->getMessage()
	      );
	      return $response->withJson($message);
	   }
	}
	/*
		a function to update a user record in the
		database, @user_id, @token, @firstname, @lastname, @email, @mobile, @password
		returns a user object.
	*/
	function updateUser($request, $response) {

	   $user = $request->getParsedBody();
		$id = $request->getAttribute('id');

	   $query = "UPDATE users SET firstname=:firstname, lastname=:lastname, email=:email, mobile=:mobile, password=:password WHERE id = $id";
	   try {

	   	$database = openConnection();
	     	$password = md5($user['password']);
        	$stmt = $database->prepare($query);

         $stmt->bindParam('token', $user['token']);
	      $stmt->bindParam('firstname', $user['firstname']);
	      $stmt->bindParam('lastname', $user['lastname']);
	      $stmt->bindParam('email', $user['email']);
	      $stmt->bindParam('mobile', $user['mobile']);
	      $stmt->bindParam('password', $password);
        	$stmt->execute();

        	$database = null;
        	$message = array(
     			'status' => true,
     			'message' => "Congratulations, a user account (".$user['email'].") was updated successfully"
	     	);
	      return $response->withJson($message);
	   } catch(PDOException $exception) {
	        $error = array( 'message' => $exception->getMessage());
	        return $response->withJson($error);
	   }
	}
	/*
		a function to delete a user record from the
		database, @user_id, returns a deleted user object.
	*/
	function deleteUser($request, $response) {
		$id = $request->getAttribute('id');
	   $sql = "DELETE FROM users WHERE id=:id";
	   try {
	      $database = openConnection();
	      $stmt = $database->prepare($sql);
	      $stmt->bindParam('id', $id);
	      $stmt->execute();
	      $database = null;
			$message = array(
	      	'status' => true,
	      	'message' => 'User with id '.$id.' was deleted'
	      );
			return $response->withJson($message);
	   }catch(PDOException $exception) {
	      $error = array( 'message' => $exception->getMessage());
	      return $response->withJson($error);
	   }
	}
	/*
		a function to check if an email or mobile number already exists in the
		database, @email, @mobile returns boolean.
	*/
	function validateEmail($email, $mobile){
		if(check_email($email)){
			$query = "SELECT * FROM users WHERE email = '$email' AND mobile = '$mobile' ";
			try {
				$stmt = openConnection()->query($query);
				$result = $stmt->fetchAll(PDO::FETCH_OBJ);
				$found = count($result);
				return ($found > 0) ? true : false;
			}catch (PDOException $exception) {
				$message = array( 'message' => $exception->getMessage());
		      return $response->withJson($error);
			}
		} else {
			$message = array( 'message' => 'Invalid Email address');
		   return $response->withJson($message);
		}
	}
	/*
		a function to authenticate a user
		params @email, @mobile, @password returns a boolean.
	*/
	function authenticate($request, $response) {

		$data = $request->getParsedBody();

		$username = $data['username'];
		$password = md5($data['password']);

	   $sql =  "SELECT * FROM users 
	   			WHERE email = '$username' 
	   			OR mobile = '$username' 
	   			AND password = '$password' ";
	   try {

	      $stmt = openConnection()->query($sql);
			$user = $stmt->fetch(PDO::FETCH_OBJ);

			if ($user) {
				$data = array(
					'status' => true,
					'message' => 'User found',
					'user' => $user 
				);
			}else{
				$data = array(
					'status' => false,
					'message' => 'No User found matching your records'
				);
			}
			$db = null;
			return $response->withJson($data);
	   } catch(PDOException $exception) {
	      $error = array( 'message' => $exception->getMessage());
	      return $response->withJson($error);
	   }
	}
	/*
		a function to validate a user email
		@email, returns a boolean.
	*/
	function check_email($email){
		$regExp = "/^[a-z0-9._+-]{1,64}@(?:[a-z0-9-]{1,63}\.){1,125}[a-z]{2,63}$/";
		return preg_match($regExp, $email);
	}
	
	