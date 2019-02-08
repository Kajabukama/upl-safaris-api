<?php

	require __DIR__ . '/../config/database.php';
	
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

	function recentUsers($request, $response) {
		$data = array(
			'data' => 'Not available',
			'status' => true,
			'total' => 0
		);
		return $response->withJson($data);
	}
	
	