<?php

	require __DIR__ . '/../config/core.php';
	require __DIR__ . '/../config/config.php';
	/*
	*	a function to return photo record from
	*/
	function select_photo($request, $response) {
		$sql =  "SELECT * FROM ".TBL_PHOTO;
		try {
			$stmt = openConnection()->query($sql);
			$photos = $stmt->fetchAll(PDO::FETCH_OBJ);
			$db = null;

			if (count($photos) > 0) {
				$data = array( 
					'data' => $photos, 
					'total' => count($photos), 
					'status' => true 
				);
				return $response->withJson($data);
			} else {
				$data = array( 
					'data' => null, 
					'total' => count($photos), 
					'status' => false 
				);
				return $response->withJson($data);
			}
		} catch (PDOException $exception) {
			$error = array( 'message' => $exception->getMessage());
	      return $response->withJson($error);
		}
	}

	function select_csv($uid){
		$query = "SELECT * FROM tbl_csv WHERE uid = $uid";
		$stmt = openConnection()->query($query);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	function photo_upload($request,$response){

		$upload = $request->getParsedBody();
		$uid = $upload['uid'];
		$sid = $upload['sid'];
		define('DIR_SCHOOL', $sid);

		$status_message = $error_message = $values = $error_upload = $error_upload_type = '';
		$total = 0;
		
		if(!empty(array_filter($_FILES['files']['name']))){
			if (mkdir(TARGET_DIR . DIR_SCHOOL .DS)) {
				foreach($_FILES['files']['name'] as $key => $val){

					$csv = select_csv($uid);
					foreach($csv as $key => $v) {

						$number = mt_rand(0,999999);
						$name = basename($_FILES['files']['name'][$key]);

						$destination = TARGET_DIR . DIR_SCHOOL .DS. $name;
						$type = pathinfo($destination, PATHINFO_EXTENSION);

						if(in_array($type, ALLOWED_TYPE)){
							if(move_uploaded_file($_FILES['files']["tmp_name"][$key], $destination)){
								$values .= "($v[uid], '".$v['sid']."', '".$v['indexno']."', '".$v['student_name']."', '".$name."'),";
								crop_image($destination, 132);
								$total = $total + 1;
							}else{
								$error_upload .= $_FILES['files']['name'][$key].', ';
							}
						}else{
							$error_upload_type .= $_FILES['files']['name'][$key].', ';
						}
					}
				}
			}
			if(!empty($values)){

				$values = trim($values,',');
				$database = openConnection();
				
				$query = "INSERT INTO tbl_photos (uid, sid, indexno, student_name, thumb) VALUES $values";
				$stmt = $database->query($query); 
				if($stmt){
					$message = array(
						'status' => true,
						'total' => $total,
						'message' => "Files are uploaded successfully."
					);
					return $response->withJson($message);
				}else{
					$message = array(
						'status' => false,
						'message' => "Sorry, there was an error uploading your file."
					);
					return $response->withJson($message);
				}
			}
		}else{
			$message = array(
				'status' => false,
				'message' => 'Please select a file to upload.'
			);
			return $response->withJson($message);
		}
	}

	function csv_upload($request,$response){

		$upload = $request->getParsedBody();
		$uid = $upload['uid'];
		$sid = $upload['sid'];

		if (!empty($_FILES['file']['name'])) {

			$file_name = basename($_FILES['file']['name']);
			$temp_name = $_FILES['file']['tmp_name'];

			$destination = TARGET_DIR . $file_name;

			$values_SQL = "";

			$tmp = explode('.', $file_name);
			$extension = end($tmp);

			if (in_array($extension, ALLOWED_TYPE)) {

				$file_data = fopen($temp_name, 'r');
				fgetcsv($file_data);

				while($row = fgetcsv($file_data)){
					$values_SQL .= "($uid, '".$sid."', '".$row[0]."', '".$row[1]."'),";
				}

				if (!empty($values_SQL)) {
					$database = openConnection();
					$values_SQL = trim($values_SQL,',');

					$query = $database->query("INSERT INTO TBL_CSV (uid, sid, indexno, student_name) VALUES $values_SQL");
					if ($query) {
						$message = array(
							'data' => array(
								'status' => true,
								'message' => 'Successfully imported'
							)
						);
					} else {
						$message = array(
							'data' => array(
								'status' => false,
								'message' => 'Data import failed'
							)
						);
					}
					return $response->withJson($message);
				}
			} else {
				$message = array(
					'status' => false,
					'message' => 'You did not select a file to Upload.'
				);
				return $response->withJson($message);
			}
		}
	}

	function photo_by_uploader($request, $response) {

		$userid = $request->getAttribute('uid');
		$sql =  "SELECT * FROM tbl_photos WHERE uid = $userid ";
		try {
			$stmt = openConnection()->query($sql);
			$photos = $stmt->fetchAll(PDO::FETCH_OBJ);
			$db = null;
			return $response->withJson($photos);
		} catch (PDOException $exception) {
			$error = array( 'message' => $exception->getMessage());
	      return $response->withJson($error);
		}
	}
	function photo_school($request, $response) {

		$name = $request->getAttribute('name');

		$sql =  "SELECT * FROM TBL_PHOTO WHERE school = '$name'";
		try {
			$stmt = openConnection()->query($sql);
			$photos = $stmt->fetchAll(PDO::FETCH_OBJ);
			$db = null;
			return $response->withJson($photos);
		} catch (PDOException $exception) {
			$error = array( 'message' => $exception->getMessage());
	      return $response->withJson($error);
		}
	}

	function photo_student($request, $response) {
		$indexno = $request->getAttribute('indexno');
		$sql =  "SELECT * FROM TBL_PHOTO WHERE id = $indexno ";
		try {
			$stmt = openConnection()->query($sql);
			$photo = $stmt->fetchAll(PDO::FETCH_OBJ);
			$db = null;
			return $response->withJson($photo);
		} catch (PDOException $exception) {
			$error = array( 'message' => $exception->getMessage());
	      return $response->withJson($error);
		}
	}

	function list_students($request, $response) {
		$uid = $request->getAttribute('uid');

		$query = "SELECT * FROM tbl_csv WHERE uid = $uid";
		$stmt = openConnection()->query($query);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $response->withJson($result);
	}
	function list_photos($request, $response) {
		$uid = $request->getAttribute('uid');

		$sql =  "SELECT * FROM TBL_PHOTO WHERE uid = $uid";
		try {
			$stmt = openConnection()->query($sql);
			$photos = $stmt->fetchAll(PDO::FETCH_OBJ);
			$db = null;
			return $response->withJson($photos);
		} catch (PDOException $exception) {
			$error = array( 'message' => $exception->getMessage());
	      return $response->withJson($error);
		}
	}

