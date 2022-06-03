<?php

require_once('./database.php');

class TienditaVentas
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
    $folioventa = $_POST['folioventa'];
    $cantidad_productos = $_POST['cantidad_productos'];
    $total = $_POST['total'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "INSERT INTO ventas (folioventa, cantidad_productos, total) VALUES ('$folioventa', '$cantidad_productos', '$total')";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Venta creado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al crear el venta')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function update()
  {
    $id = $_POST['id'];
    $folioventa = $_POST['folioventa'];
    $cantidad_productos = $_POST['cantidad_productos'];
    $total = $_POST['total'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "UPDATE ventas SET folioventa = '$folioventa', cantidad_productos = '$cantidad_productos', total = '$total' WHERE id = '$id'";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Venta actualizado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al actualizar el venta')));
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
      $sql = "DELETE FROM ventas WHERE id = '$id'";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Venta eliminado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al eliminar el venta')));
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
      $result = $con->query(Constants::$SQL_SELECT_ALL_VENTAS);
      if ($result->num_rows > 0) {
        $ventas = array();
        while ($row = $result->fetch_array()) {
          array_push($ventas, array('id' => $row['id'], 'folioventa' => $row['folioventa'], 'cantiadad_productos' => $row['cantiadad_productos'], 'total' => $row['total']));
        }
        print(json_encode(array('code' => 1, 'message' => 'Ventas obtenidos correctamente', 'result' => $ventas)));
      } else {
        print(json_encode(array('code' => 0, 'message' => 'No hay ventas')));
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

    $sql = "SELECT * FROM ventas WHERE folioventa LIKE '%$query%' LIMIT $limit OFFSET $start";
    $con = $this->connect();
    if ($con != null) {
      $result = $con->query($sql);
      if ($result->num_rows > 0) {
        $ventas = array();
        while ($row = $result->fetch_array()) {
          array_push($ventas, array('id' => $row['id'], 'folioventa' => $row['folioventa'], 'cantidad_productos' => $row['cantidad_productos'], 'total' => $row['total']));
        }
        print(json_encode(array('code' => 1, 'message' => 'Productos obtenidos correctamente', 'result' => $ventas)));
      } else {
        print(json_encode(array('code' => 0, 'message' => 'No hay productos')));
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

$u = new TienditaVentas();
$u->handleRequest();
