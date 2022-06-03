<?php

require_once('./database.php');

class TienditaClientes
{
  public function connect()
  {
    $con = new mysqli(Constants::$DB_SERVER, Constants::$DB_USER, Constants::$DB_PASSWORD, Constants::$DB_NAME);
    if ($con->connect_error) {
      return null;
    } else {
      return $con;
    }
  }

  public function insert()
  {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "INSERT INTO clientes (nombre, direccion, telefono, correo) VALUES ('$nombre', '$direccion', '$telefono', '$correo')";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Cliente creado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al crear el cliente')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function update()
  {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "UPDATE clientes SET nombre = '$nombre', direccion = '$direccion', telefono = '$telefono', correo = '$correo' WHERE id = '$id'";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Cliente actualizado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al actualizar el cliente')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function delete()
  {
    $id = $_POST['id'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "DELETE FROM clientes WHERE id = '$id'";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Cliente eliminado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al eliminar el cliente')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function select()
  {
    $con = $this->connect();
    if ($con != null) {
      $result = $con->query(Constants::$SQL_SELECT_ALL_CLIENTES);
      if ($result->num_rows > 0) {
        $clientes = array();
        while ($row = $result->fetch_array()) {
          array_push($clientes, array('id' => $row['id'], 'nombre' => $row['nombre'], 'direccion' => $row['direccion'], 'telefono' => $row['telefono'], 'correo' => $row['correo']));
        }
        print(json_encode(array('code' => 1, 'message' => 'Clientes obtenidos correctamente', 'result' => $clientes)));
      } else {
        print(json_encode(array('code' => 0, 'message' => 'No hay clientes')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function search()
  {
    $query = $_POST['query'];
    $limit = $_POST['limit'];
    $start = $_POST['start'];

    $sql = "SELECT * FROM clientes WHERE nombre LIKE '%$query%' LIMIT $limit OFFSET $start";
    $con = $this->connect();
    if ($con != null) {
      $result = $con->query($sql);
      if ($result->num_rows > 0) {
        $clientes = array();
        while ($row = $result->fetch_array()) {
          array_push($clientes, array('id' => $row['id'], 'nombre' => $row['nombre'], 'direccion' => $row['direccion'], 'telefono' => $row['telefono'], 'correo' => $row['correo']));
        }
        print(json_encode(array('code' => 1, 'message' => 'Clientes obtenidos correctamente', 'result' => $clientes)));
      } else {
        print(json_encode(array('code' => 0, 'message' => 'No hay clientes')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function handleRequest()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'INSERT') {
          $this->insert();
        } else if ($action == 'UPDATE') {
          $this->update();
        } else if ($action == 'DELETE') {
          $this->delete();
        } else if ($action == 'GET_PAGINATED') {
          $this->search();
        } else if ($action == 'GET_PAGINATED_SEARCH') {
          $this->search();
        } else {
          print(json_encode(array('code' => 4, 'message' => 'Acción no reconocida')));
        }
      } else {
        print(json_encode(array('code' => 5, 'message' => 'Acción no especificada')));
      }
    } else {
      $this->select();
    }
  }
}

$u = new TienditaClientes();
$u->handleRequest();
