
/* 
  El evento "shown.bs.offcanvas" es fundamental, ya que garantiza que las variables no se obtengan 
  hasta que el offcanvas esté abierto. Sin este evento, la variable no se actualiza 
  hasta que se recargue home.php.

  Este evento se activa cuando el offcanvas se muestra por completo, permitiendo 
  obtener y utilizar valores actualizados de la página.

  En este caso, se utiliza para obtener el contenido actualizado de 'NomProyecto' 
*/

// Declaramos la variable fuera de la función para que sea accesible en todo el ámbito
var red_proyecto;
var nodes = [];
var links = [];


// Solucion temporal que hace que cuando se pulse el buton de close del "offcanvas" se recargue la pagina se muestran nuevos nodos.
document.getElementById('closeButton').addEventListener('click', function () {
  // Recarga la página
  location.reload();
});



document.getElementById('offcanvasBottom').addEventListener('shown.bs.offcanvas', function () {

  // Definimos una función que contendrá el código que queremos ejecutar después del evento
  function obtenerNodos() {
    return new Promise(function(resolve, reject) {
      $.ajax({
        type: 'POST',
        url: 'funciones.php',
        data: { red_proyecto: red_proyecto },
        success: function(response) {
          var datosProyecto = JSON.parse(response);
          var nodes = datosProyecto.nodes;
          var links = datosProyecto.links;
          resolve({ nodes, links }); // Resuelve la Promesa con nodos y enlaces
        },
        error: function(error) {
          reject(error); // Rechaza la Promesa en caso de error
        }
      });
    });
  }
  
 

  //Obtiene el nombre del proyecto desde el elemento #NomProyecto DOM.
  red_proyecto = document.getElementById('NomProyecto').innerHTML;
  

  // Obtener el elemento h5 por su id
  var offcanvasBottomLabel = document.getElementById('offcanvasBottomLabel');

  // Verificar si el elemento existe
  if (offcanvasBottomLabel) {
    // Cambiar el contenido del h5
    offcanvasBottomLabel.textContent = 'Red De ' + red_proyecto; 
  }


  obtenerNodos().then(function(result) {
    // Ahora puedes trabajar con los nuevos nodos
    //console.log(result.nodes);
    console.log(result.links);

    // Asigna los valores a las variables globales
    nodes = result.nodes;
    links = result.links;

    // Resto del código que depende de los nuevos nodos

      var tooltip = d3.select("#cuerpo")
        .append("div")
        .attr("class", "tooltip")
        .style("opacity", 0);

      // Compute the distinct nodes from the links.
      links.forEach(function (link) {
        link.source = nodes.find(node => node.id === link.source) || { id: link.source };
        link.target = nodes.find(node => node.id === link.target) || { id: link.target };
      });

      var width = 1900,
        height = 800;

      var force = d3.layout.force()
        .nodes(d3.values(nodes))
        .links(links)
        .size([width, height])
        .linkDistance(280)
        .charge(-600)
        .on("tick", tick)
        .start();

      var svg = d3.select("#cuerpo").append("svg")
        .attr("width", width)
        .attr("height", height)
        .attr("id", "miSVG");

      var link = svg.selectAll(".link")
        .data(force.links())
        .enter().append("line")
        .attr("class", "link");

      var node = svg.selectAll(".node")
        .data(force.nodes())
        .enter().append("g")
        .attr("class", "node")
        .on("mouseover", mouseover)
        .on("mouseout", mouseout)
        .on("click", click)
        .call(force.drag);


// Representa la ubicación de los nodos 
var groups = Array.from(new Set(nodes.map(d => d.ubicacion)));

// Crear una escala de colores ordinal
var colorScale = d3.scale.category10().domain(groups);

  // Obtener un array con los grupos y los colores asignados
  var gruposColores = groups.map(function(ubicacion) {
      return { ubicacion: ubicacion, color: colorScale(ubicacion) };
  });



// Mostrar la información de las conexiones en el div #divConexiones
var idsEnlaces = Array.from(new Set(links.map(l => l.id)));

// Mostrar la información en el div #divConexiones
var ConexionesDiv = d3.select("#divConexiones");

// Crear elementos div para cada conexión
var conexionesDivs = ConexionesDiv.selectAll("div")
    .data(idsEnlaces) // Usar los IDs únicos en lugar de los datos originales
    .enter()
    .append("div")
    .attr("class", "d-flex justify-content-between align-items-center mb-2"); // Utilizar clases de Bootstrap para alinear los elementos

// Agregar checkboxes y texto
conexionesDivs.append("label")
    .attr("class", "list-group-item d-block w-100")
    .html(function (id) {
        // Obtener el objeto correspondiente al ID actual
        var currentLink = links.find(link => link.id === id);

        return `
            <input class="form-check-input me-1 conexion-checkbox" type="checkbox" value="${id}">
            ${currentLink.source.nombre} - ${currentLink.target.nombre}`;
    });

// Función para borrar conexiones
function borrarConexiones() {
  var checkboxes = d3.selectAll(".conexion-checkbox:checked");

  // Mapear las promesas de AJAX
  var promesas = checkboxes[0].map(function (checkbox) {
    var id_conexion = checkbox.value;
    return new Promise(function(resolve, reject) {
      $.ajax({
        type: 'POST',
        url: 'funciones.php',
        data: { id_conexion: id_conexion },
        success: function(response) {
          location.reload();
          resolve({ response }); // Resuelve la Promesa con nodos y enlaces
        },
        error: function(error) {
          reject(error); // Rechaza la Promesa en caso de error
        }
      });
    });
  });

  // Utilizar Promise.all para esperar que todas las solicitudes AJAX se completen
  Promise.all(promesas)
    .then(function(responses) {
      // Todas las solicitudes AJAX han sido completadas con éxito
      console.log("Borrando conexiones con IDs:", responses);
    })
    .catch(function(error) {
      // Se ejecutará si alguna de las solicitudes AJAX falla
      console.error("Error al borrar conexiones:", error);
    });
}

// Función para agregar nueva conexión
function agregarConexion() {
  // Obtener los valores seleccionados de los select
  var origenConexion = document.getElementById("selectOrigen").value;
  var destinoConexion = document.getElementById("selectDestino").value;
  var proyectoConexion = red_proyecto;
  // Realizar la petición AJAX
  $.ajax({
      type: 'POST',
      url: 'funciones.php', // Reemplaza con la ruta correcta
      data: {
        proyectoConexion: proyectoConexion,
        origenConexion: origenConexion,
        destinoConexion: destinoConexion
      },
      success: function (response) {
          // Manejar la respuesta del servidor si es necesario
          location.reload();
          resolve({ response });
      },
      error: function (error) {
          // Manejar el error si ocurre
          console.error("Error en la petición AJAX (agregar conexion):", error);
      }
  });
}


// Mostrar la información en el div #selectNodos
var selectNodosDiv = d3.select("#selectNodos");

// Crear elementos select para cada columna
var selectNodosSelects = selectNodosDiv.selectAll("div")
    .data(d3.range(2)) // Crear dos elementos select (para las dos columnas)
    .enter()
    .append("div")
    .attr("class", "col")
    .append("select")
    .attr("class", "form-select")
    .attr("id", function (d, i) {
        return i === 0 ? "selectOrigen" : "selectDestino";
    });


// Agregar la opción "De" al primer select
selectNodosSelects.filter((d, i) => i === 0)
    .selectAll("option")
    .data([null]) // Un solo elemento para la opción "De"
    .enter()
    .append("option")
    .attr("value", "De")
    .text("De");

// Agregar la opción "A" al segundo select
selectNodosSelects.filter((d, i) => i === 1)
    .append("option")
    .attr("value", "A")
    .text("A");

// Agregar opciones adicionales a cada select
selectNodosSelects.selectAll("option:not(:first-child)")
    .data(nodes)
    .enter()
    .append("option")
    .attr("value", function (d) { return d.id; })
    .text(function (d) { return d.nombre; });


// Asociar funciones a los botones mediante addEventListener
document.getElementById("borrarBtn").addEventListener("click", borrarConexiones);
document.getElementById("agregarBtn").addEventListener("click", agregarConexion);







      







  // Asignar colores a los nodos en función de sus grupos
  node.append("circle")
      .attr("r", 35)
      .style("fill", function(d) {
          // Asigna colores basados en los grupos
          return colorScale(d.ubicacion);
      })
      .style("opacity", 0.6);


      //Asignar iconos segun el tipo de dispositivo.
      node.append("image")
        .attr("x", "-25px")
        .attr("y", "-25px")
        .attr("width", "52px")
        .attr("height", "52px")
        .attr("xlink:href", function(d) {
          // Asignar icono basado en la propiedad "tipo" del nodo
          if (d.tipo === "Router") {
              return "./vendor/d3-network/icons/router.png";
          } else if (d.tipo === "Firewall") {
              return "./vendor/d3-network/icons/firewall.png";
          } else if (d.tipo === "Switch") {
              return "./vendor/d3-network/icons/switch.png";
          } else {
          }
        });

        node.append("text")
        .attr("x", 42)
        .attr("dy", "0.6em") // Mueve la segunda línea hacia arriba
        .text(function (d) { return d.nombre; })
        .append("tspan")
        .attr("x", 42)
        .attr("dy", "1em") // Mueve la segunda línea hacia abajo
        .text(function (d) { return d.direccion_ip; });

      function tick() {
        link
          .attr("x1", function (d) { return d.source.x; })
          .attr("y1", function (d) { return d.source.y; })
          .attr("x2", function (d) { return d.target.x; })
          .attr("y2", function (d) { return d.target.y; });

        node
          .attr("transform", function (d) { return "translate(" + d.x + "," + d.y + ")"; });
      }

      function mouseover(d) {
        d3.select(this).select("circle").transition()
          .duration(750)
          .attr("r", 32);
        
      }

      function mouseout() {
        d3.select(this).select("circle").transition()
            .duration(600)  // Ajusta la duración aquí
            .attr("r", 35);
    
        d3.selectAll(".tooltip")
            .transition()
            .duration(600)  // Ajusta la duración aquí
            .style("opacity", 0);
    }
    

      function click(d) {
        tooltip.transition()
          .duration(200)
          .style("opacity", .9);

          tooltip.html("ID: " + d.id + "<br/>" +
                  "Nombre: " + d.nombre + "<br/>" +
                  "Tipo: " + d.tipo + "<br/>" +
                  "MAC: " + d.direccion_mac + "<br/>" +
                  "IP: " + d.direccion_ip + "<br/>" +
                  "Protocolo de Acceso: " + d.protocolo_acceso + "<br/>" +
                  "Ubicación: " + d.ubicacion + "<br/>")
          .style("left", (d.x + 40) + "px")
          .style("top", (d.y - 80) + "px")
          .style("position", "absolute")
          .style("background-color", "rgba(33,37,41, 15)")
          .style("color", "#fff")
          .style("padding", "10px")
          .style("border-radius", "10px")
          .style("border", "none");
        // Fijar o desfijar el nodo al hacer clic
        d.fixed = true;
      }

      
    // ...
  }).catch(function(error) {
    console.error('Error al obtener nodos:', error);
  });

});


