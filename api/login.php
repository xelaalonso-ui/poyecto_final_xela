header("Content-Type: application/json");
require "../includes/db_connection.php";

$data=json_decode(file_get_contents("php://input"));

$stmt=mysqli_prepare($conn,
"SELECT password_hash FROM Usuario WHERE username=?");
mysqli_stmt_bind_param($stmt,"s",$data->usuario);
mysqli_stmt_execute($stmt);
$res=mysqli_stmt_get_result($stmt);
$user=mysqli_fetch_assoc($res);

echo json_encode([
 "status"=>$user && password_verify($data->password,$user['password_hash'])
]);
?>
