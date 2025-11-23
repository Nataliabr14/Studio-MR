<?php
$conexion = new mysqli("localhost", "root", "", "manuela_esthetic");
if ($conexion->connect_error) { die("Error de conexión: " . $conexion->connect_error); }

// OBTENER SERVICIOS Y CATEGORÍAS
$sql = "SELECT s.id_servicio, s.nombre_servicio, c.nombre_categoria
        FROM servicios s
        LEFT JOIN categorias c ON s.id_categoria = c.id_categoria
        ORDER BY c.nombre_categoria, s.nombre_servicio";

$servicios = $conexion->query($sql);

// MODELOS DE PESTAÑAS
$modelos_pestanas = $conexion->query("SELECT * FROM modelos_pestanas");
?>

<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Reservas - Manuela Esthetic</title>

<link rel='stylesheet' href='./assets/css/styles.css'>
<link rel='stylesheet' href='./assets/css/reservas.css'>
<script src='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js'></script>

<style>.hidden {display:none;}</style>
</head>

<body>

<header>
   <h1 class='logo'>
      <span class='mr'>MR</span>
      <span class='nombre'>Manuela Esthetic</span><br>
      <span class='subtexto'>Cosmetología y Estética</span>
   </h1>

   <div class="menu-container">
        <div class="hamburger" id="hamburger">
            <span></span><span></span><span></span>
        </div>

        <nav class="nav-menu" id="navMenu">
            <a href="index.html">Inicio</a>
            <a href="nosotros.html">Nosotros</a>
            <a href="servicios.html">Servicios</a>
            <a href="reservas.php">Reservas</a>
            <a href="contacto.html">Contacto</a>
        </nav>
    </div>
</header>

<section class='reserva-section'>
<h2>Agenda tu cita</h2>
<p>Completa los datos para reservar</p>

<form action='guardar_reserva.php' method='POST' class='form-reserva'>

<label>Nombre completo</label>
<input type='text' name='nombre' required>

<label>Teléfono</label>
<input type='text' name='telefono' required>

<!-- SERVICIOS -->
<label>Selecciona un servicio</label>
<select id='servicio' name='servicio' required>
<option value=''>-- Seleccionar --</option>

<?php
$categoria_actual = '';
while ($s = $servicios->fetch_assoc()) {

    if ($categoria_actual != $s['nombre_categoria']) {
        if ($categoria_actual != '') echo "</optgroup>";
        $categoria_actual = $s['nombre_categoria'];
        echo "<optgroup label='{$categoria_actual}'>";
    }

    echo "<option data-categoria='{$s['nombre_categoria']}' 
                 data-nombre='{$s['nombre_servicio']}'
                 value='{$s['id_servicio']}'>
          {$s['nombre_servicio']}
          </option>";
}
echo "</optgroup>";
?>
</select>

<!-- MODELOS PESTAÑAS -->
<div id="grupoPestanas" class="hidden">
    <label>Modelo de pestañas</label>
    <select id="modelo_pestanas" name="modelo_pestanas">
        <option value="">-- Seleccionar modelo --</option>

        <?php while ($p = $modelos_pestanas->fetch_assoc()) { ?>
            <option 
                data-idservicio="<?php echo $p['id_servicio']; ?>" 
                data-full="<?php echo $p['precio_full']; ?>"
                data-retoque="<?php echo $p['precio_retoque']; ?>"
                value="<?php echo $p['id_modelo']; ?>">
                <?php echo $p['nombre_modelo']; ?>
            </option>
        <?php } ?>
    </select>
</div>

<!-- PRECIOS FULL / RETOQUE PARA PESTAÑAS -->
<div id="grupoPestanasPrecios" class="hidden">
    <label>Tipo de aplicación</label>
    <select id="precio_pestanas" name="precio_pestanas">
        <option value="">-- Seleccionar tipo --</option>
    </select>
</div>

<!-- PRECIOS LABIOS (SIN MODELOS) -->
<div id="grupoLabios" class="hidden">
  <label>Tipo de aplicación</label>
  <select id="precio_labios" name="precio_labios">
      <option value="">-- Seleccionar opción --</option>
  </select>
</div>

<label>Fecha</label>
<input type='date' name='fecha' required>

<label>Hora</label>
<input type='time' name='hora' required>

<button type='submit' class='btn'>Reservar ahora</button>

</form>
</section>

<script>
// --------- PESTAÑAS: MODELOS Y PRECIOS ---------
const modeloP = document.getElementById("modelo_pestanas");
const precioP = document.getElementById("precio_pestanas");
const grupoPrecios = document.getElementById("grupoPestanasPrecios");

modeloP.addEventListener("change", function() {

    precioP.innerHTML = '<option value="">-- Seleccionar tipo --</option>';
    grupoPrecios.classList.add("hidden");

    let opcion = modeloP.options[modeloP.selectedIndex];
    if (!opcion.value) return;

    let full = opcion.getAttribute("data-full");
    let retoque = opcion.getAttribute("data-retoque");

    grupoPrecios.classList.remove("hidden");

    precioP.innerHTML += `
        <option value="${full}">Full — ${full} COP</option>
        <option value="${retoque}">Retoque — ${retoque} COP</option>
    `;
});

// --------- DETECTAR CATEGORÍA DEL SERVICIO ---------
const servicio = document.getElementById("servicio");
const grupoPestanas = document.getElementById("grupoPestanas");
const grupoLabios = document.getElementById("grupoLabios");

servicio.addEventListener("change", function() {

    const categoria = this.options[this.selectedIndex].dataset.categoria;
    let idServ = parseInt(this.value);

    grupoPestanas.classList.add("hidden");
    grupoLabios.classList.add("hidden");
    grupoPrecios.classList.add("hidden");

    // ------ PESTAÑAS ------
    if (categoria === "Pestañas") {
        grupoPestanas.classList.remove("hidden");
    }

    // ------ LABIOS ------
    if (categoria === "Labios") {

        // limpiar opciones
        precio_labios.innerHTML = '<option value="">-- Seleccionar opción --</option>';

        // Micropigmentación (37)
        if (idServ === 37) {
            grupoLabios.classList.remove("hidden");
            precio_labios.innerHTML += `
                <option value="150000">Full — 150.000 COP</option>
                <option value="100000">Retoque — 100.000 COP</option>
            `;
        }

        // Neutralización (38)
        if (idServ === 38) {
            grupoLabios.classList.remove("hidden");
            precio_labios.innerHTML += `
                <option value="150000">Full — 150.000 COP</option>
                <option value="100000">Retoque — 100.000 COP</option>
            `;
        }

        // Hidralips (39) → NO muestra selector
        if (idServ === 39) {
            grupoLabios.classList.add("hidden");
        }
    }
});
</script>

</body>
</html>








