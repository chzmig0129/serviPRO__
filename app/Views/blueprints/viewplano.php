<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Agregar en el head -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* Estilos para el contenedor del plano */
    #planoContainer {
        position: relative;
        width: 100%;
        height: 600px;
        overflow: auto;
        border: 1px solid #ccc;
        background-color: #f5f5f5;
    }
    
    /* Estilos para la imagen del plano */
    #planoImage {
        max-width: 100%;
        display: none;
    }
    
    /* Estilos para el texto de placeholder */
    #placeholderText {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #999;
        text-align: center;
    }
    
    /* Estilos para los marcadores de trampas */
    .trap-marker {
        position: absolute;
        width: 24px;
        height: 24px;
        background-color: rgba(255, 0, 0, 0.7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        cursor: pointer;
        z-index: 10;
        transition: all 0.2s ease;
        padding: 5px;
    }
    
    .trap-marker:hover {
        transform: translate(-50%, -50%) scale(1.2);
        background-color: rgba(255, 0, 0, 0.9);
    }
    
    .trap-marker.highlighted {
        background-color: #ff6600;
        box-shadow: 0 0 0 3px rgba(255, 102, 0, 0.5);
    }
    
    /* Estilos para el tooltip */
    .trap-tooltip {
        position: absolute;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 100;
        display: none;
    }
    
    .trap-tooltip button {
        background-color: #4a90e2;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        display: flex;
        align-items: center;
    }
    
    .trap-tooltip button:hover {
        background-color: #3a80d2;
    }
    
    /* Estilos para las zonas */
    .zone-polygon, .zona {
        position: absolute;
        background-color: rgba(0, 128, 255, 0.2);
        border: 2px dashed rgba(0, 128, 255, 0.5);
        pointer-events: none;
        cursor: move;
    }
    
    /* Estilos para el dropdown de trampas */
    .trap-menu {
        position: absolute;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 100;
        width: 200px;
    }
    
    .trap-menu a {
        display: block;
        padding: 8px 12px;
        color: #333;
        text-decoration: none;
    }
    
    .trap-menu a:hover {
        background-color: #f5f5f5;
    }
    
    /* Animaciones */
    @keyframes highlight {
        0% { box-shadow: 0 0 0 0 rgba(255, 102, 0, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(255, 102, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 102, 0, 0); }
    }
    
    @keyframes pulse {
        0% { transform: translate(-50%, -50%) scale(1); }
        50% { transform: translate(-50%, -50%) scale(1.3); background-color: #ff6600; }
        100% { transform: translate(-50%, -50%) scale(1); }
    }
    
    /* Estilos para las filas seleccionadas en la tabla */
    tr.selected {
        background-color: #e6f2ff !important;
    }

    /* Estilos adicionales para zonas específicas */
    .zona-circulo {
        border-radius: 50%;
    }

    .zona-poligono {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(100, 100, 100, 0.1);
        border: 2px dashed #666;
        pointer-events: auto;
        cursor: pointer;
    }

    .zona-poligono-temporal {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 0, 0.2);
        border: 2px dashed #ff0;
    }

    .punto-poligono {
        position: absolute;
        width: 8px;
        height: 8px;
        background: #ff0;
        border: 1px solid #000;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        z-index: 20;
    }

    .zona-texto {
        position: absolute;
        background: rgba(255, 255, 255, 0.8);
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        transform: translate(-50%, -50%);
        z-index: 15;
        pointer-events: none;
        white-space: nowrap;
    }

    .resize-handle {
        position: absolute;
        width: 10px;
        height: 10px;
        background: white;
        border: 1px solid #666;
        cursor: se-resize;
    }

    @keyframes highlight {
        0% { background-color: rgba(255, 255, 0, 0.5); }
        100% { background-color: transparent; }
    }

    .trap-tooltip {
        position: absolute;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        z-index: 1000;
        display: none;
        min-width: 150px;
        animation: fadeIn 0.2s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .trap-tooltip button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .trap-tooltip button:hover {
        background-color: #45a049;
    }
    
    .trap-marker.highlighted {
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.7);
        transform: scale(1.2);
        z-index: 1000;
    }
</style>

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">Sistema de Mapeo de Trampas</h1>
            <p class="text-gray-500"><?= $plano['nombre'] ?> - <?= $sede['nombre'] ?></p>
        </div>
        <div class="flex gap-3">
            <button id="btnSeleccionarImagen" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Seleccionar Imagen
            </button>
            <button id="btnGuardar" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save"></i>
                Guardar Estado
            </button>
            <button id="btnDescargar" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-download"></i>
                Descargar Estado
            </button>
            <button id="btnCargar" class="flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-upload"></i>
                Cargar Estado
            </button>
            <button id="btnLimpiar" class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-trash-alt"></i>
                Limpiar Todo
            </button>
        </div>
    </div>

    <!-- Área del Plano -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Plano -->
        <div class="lg:col-span-3 bg-white rounded-lg shadow-md p-4">
            <div id="planoContainer" class="w-full h-[600px] bg-gray-100 rounded-lg flex items-center justify-center relative">
                <img id="planoImage" class="max-w-full max-h-full hidden" style="object-fit: contain;" />
                <p id="placeholderText" class="text-gray-500">Seleccione una imagen para comenzar</p>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="space-y-6">
            <!-- Herramientas -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="font-semibold mb-4">Herramientas</h3>
                <div class="space-y-2">
                    <!-- Dropdown Agregar Trampa -->
                    <div class="dropdown relative">
                        <button id="btnAgregarTrampa" class="w-full flex items-center justify-between px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mt-2">
                            <span class="flex items-center">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Agregar Trampa
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>
                        <div class="trap-menu hidden absolute w-full bg-white border rounded-lg shadow-lg z-50">
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="edc_quimicas">
                                <i class="fas fa-flask mr-2"></i> EDC Químicas
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="luz_uv">
                                <i class="fas fa-lightbulb mr-2"></i> Equipo de Luz UV
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="feromona_gorgojo">
                                <i class="fas fa-bug mr-2"></i> Trampa de Feromona Gorgojo
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="equipo_sonico">
                                <i class="fas fa-volume-up mr-2"></i> Equipo Sónico
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="globo_terror">
                                <i class="fas fa-circle mr-2"></i> Globo terror
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="atrayente_chinches">
                                <i class="fas fa-spider mr-2"></i> Trampa atrayente chinches
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="atrayente_pulgas">
                                <i class="fas fa-paw mr-2"></i> Trampa atrayente pulgas
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="feromona_picudo">
                                <i class="fas fa-seedling mr-2"></i> Trampa feromonas picudo rojo
                            </a>
                        </div>
                    </div>

                    <!-- Botón Mover Trampas -->
                    <button id="btnMoverTrampa" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-arrows-alt mr-2"></i>
                        Mover Trampas
                    </button>

                    <!-- Botón Agregar Zona -->
                    <button id="btnAgregarZona" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 mt-2">
                        <i class="fas fa-draw-polygon mr-2"></i>
                        Agregar Zona
                    </button>
                </div>
            </div>

            <!-- Lista de Trampas -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="font-semibold mb-4">Lista de Trampas</h3>
                <div class="overflow-y-auto max-h-[400px]">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Tipo</th>
                                <th class="px-4 py-2 text-left">Ubicación</th>
                                <th class="px-4 py-2 text-left">Zona</th>
                                <th class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="trampasTableBody">
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                    No hay trampas registradas
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Agregar los inputs ocultos -->
<input type="file" id="planoInput" class="form-control" accept="image/*" style="display: none;">
<input type="file" id="cargarEstadoInput" accept="application/json" style="display: none;">

<!-- Modal para Agregar Incidencia -->
<div id="modalIncidencia" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-2">Registrar Incidencia</h3>
                <div id="trampaInfo" class="mb-4 p-3 bg-gray-100 rounded-md">
                    <p class="text-sm"><strong>ID Base de Datos:</strong> <span id="trampaDbIdDisplay">-</span></p>
                    <p class="text-sm"><strong>Ubicación:</strong> <span id="trampaZonaDisplay">-</span></p>
                </div>
                <form id="formIncidencia">
                    <input type="hidden" name="trampa_id" id="trampa_id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Plaga</label>
                            <select name="tipo_plaga_select" id="tipo_plaga_select"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione un tipo</option>
                                <option value="mosca">Mosca</option>
                                <option value="cucaracha">Cucaracha</option>
                                <option value="hormiga">Hormiga</option>
                                <option value="roedor">Roedor</option>
                                <option value="otro">Otro (especificar)</option>
                            </select>
                        </div>
                        <div id="tipo_plaga_personalizado_container" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700">Especifique el tipo de plaga</label>
                            <input type="text" name="tipo_plaga_personalizado" id="tipo_plaga_personalizado" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ingrese el tipo de plaga">
                        </div>
                        <input type="hidden" name="tipo_plaga" id="tipo_plaga" required>
                        <div id="cantidad_organismos_container" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700">Cantidad de Organismos</label>
                            <input type="number" name="cantidad_organismos" id="cantidad_organismos" min="1" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ingrese la cantidad">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Incidencia</label>
                            <select name="tipo_incidencia"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Captura">Captura</option>
                                <option value="Hallazgo">Hallazgo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de Incidencia</label>
                            <input type="datetime-local" name="fecha_incidencia" id="fecha_incidencia" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Inspector</label>
                            <input type="text" name="inspector" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Nombre del inspector">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Notas Adicionales</label>
                            <textarea name="notas" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeIncidenciaModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Guardar Incidencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="<?= base_url('js/mapa.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Referencias a elementos
        const planoContainer = document.getElementById('planoContainer');
        const planoImage = document.getElementById('planoImage');
        const planoInput = document.getElementById('planoInput');
        const btnSeleccionarImagen = document.getElementById('btnSeleccionarImagen');
        const btnGuardar = document.getElementById('btnGuardar');
        const btnCargar = document.getElementById('btnCargar');
        const btnLimpiar = document.getElementById('btnLimpiar');
        const btnMoverTrampa = document.getElementById('btnMoverTrampa');
        const btnAgregarZona = document.getElementById('btnAgregarZona');
        const btnAgregarTrampa = document.getElementById('btnAgregarTrampa');
        const dropdownTrampa = document.querySelector('.dropdown');
        const cargarEstadoInput = document.getElementById('cargarEstadoInput');

        // Variables para el manejo de trampas
        let modoEdicion = null;
        let tipoTrampaSeleccionado = null;
        let contadorTrampas = 1;
        let trampaSeleccionada = null;
        let offsetX = 0;
        let offsetY = 0;

        // Agregar estas variables al inicio del script
        let dibujandoPoligono = false;
        let puntosPoligono = [];
        let poligonoTemporal = null;
        let tooltipElement = null;
        let confirmButton = null;

        // Crear el tooltip
        const tooltip = document.createElement('div');
        tooltip.className = 'trap-tooltip';
        tooltip.innerHTML = `
            <button type="button" class="add-incidence-btn">
                <i class="fas fa-plus-circle mr-2"></i>
                Agregar incidencia
            </button>
        `;
        document.body.appendChild(tooltip);

        // Variables para el manejo del tooltip y modal de incidencias
        let activeMarker = null;

        // Reposicionar las trampas después de que la página se haya cargado completamente
        setTimeout(reposicionarTrampas, 500);

        // Función para desactivar todos los botones excepto Seleccionar Imagen y Cargar
        function desactivarBotones() {
            btnGuardar.disabled = true;
            btnLimpiar.disabled = true;
            btnMoverTrampa.disabled = true;
            btnAgregarZona.disabled = true;
            btnAgregarTrampa.disabled = true;
            
            // Agregar clase para estilo desactivado
            [btnGuardar, btnLimpiar, btnMoverTrampa, btnAgregarZona, btnAgregarTrampa].forEach(btn => {
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            });
        }

        // Función para activar todos los botones
        function activarBotones() {
            btnGuardar.disabled = false;
            btnLimpiar.disabled = false;
            btnMoverTrampa.disabled = false;
            btnAgregarZona.disabled = false;
            btnAgregarTrampa.disabled = false;
            
            // Remover clase para estilo desactivado
            [btnGuardar, btnLimpiar, btnMoverTrampa, btnAgregarZona, btnAgregarTrampa].forEach(btn => {
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }

        // Desactivar botones al inicio
        desactivarBotones();

        // Cargar estado guardado en la base de datos si existe
        <?php if (!empty($plano['archivo'])): ?>
        try {
            const estadoGuardado = JSON.parse('<?= addslashes($plano['archivo']) ?>');
            if (estadoGuardado && estadoGuardado.imagen) {
                // Cargar la imagen
                planoImage.src = estadoGuardado.imagen;
                planoImage.style.display = 'block';
                document.getElementById('placeholderText').style.display = 'none';
                
                // Activar botones
                activarBotones();
                
                // Guardar las trampas en una variable global
                if (estadoGuardado.trampas && Array.isArray(estadoGuardado.trampas)) {
                    window.puntos = estadoGuardado.trampas;
                    
                    // Crear una función para colocar las trampas después de que la imagen esté completamente cargada
                    const colocarTrampas = function() {
                        // Limpiar marcadores existentes para evitar duplicados
                        document.querySelectorAll('.trap-marker').forEach(marker => marker.remove());
                        
                        // Colocar las trampas
                        window.puntos.forEach(punto => {
                            marcarTrampa(punto);
                        });
                        
                        // Actualizar la tabla
                        actualizarTablaTrampas();
                        
                        // Asegurar que todos los marcadores tengan el evento de clic
                        document.querySelectorAll('.trap-marker').forEach(marker => {
                            const newMarker = addTrapClickEvent(marker);
                            if (marker !== newMarker && marker.parentNode) {
                                marker.parentNode.replaceChild(newMarker, marker);
                            }
                        });
                        
                        // Reposicionar las trampas para asegurar que estén en la posición correcta
                        setTimeout(reposicionarTrampas, 100);
                    };
                    
                    // Si la imagen ya está cargada, colocar las trampas inmediatamente
                    if (planoImage.complete) {
                        colocarTrampas();
                    } else {
                        // Si la imagen aún no está cargada, esperar a que se cargue
                        planoImage.onload = colocarTrampas;
                    }
                }
                
                // Guardar las zonas en una variable global
                if (estadoGuardado.zonas && Array.isArray(estadoGuardado.zonas)) {
                    window.zonas = estadoGuardado.zonas;
                    
                    // Crear una función para colocar las zonas después de que la imagen esté completamente cargada
                    const colocarZonas = function() {
                        // Limpiar zonas existentes para evitar duplicados
                        document.querySelectorAll('.zona, .zona-poligono, .zona-texto').forEach(zona => zona.remove());
                        
                        // Colocar las zonas
                        window.zonas.forEach(zona => {
                            crearZonaExistente(zona);
                        });
                    };
                    
                    // Si la imagen ya está cargada, colocar las zonas inmediatamente
                    if (planoImage.complete) {
                        colocarZonas();
                    } else {
                        // Si la imagen aún no está cargada, esperar a que se cargue
                        planoImage.addEventListener('load', colocarZonas);
                    }
                }
            }
        } catch (error) {
            console.error('Error al cargar el estado guardado:', error);
        }
        <?php endif; ?>

        // Botón Seleccionar Imagen
        btnSeleccionarImagen.addEventListener('click', () => {
            planoInput.click();
        });

        planoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    planoImage.src = e.target.result;
                    planoImage.style.display = 'block';
                    document.getElementById('placeholderText').style.display = 'none';
                    // Activar botones cuando se carga la imagen
                    activarBotones();
                    
                    // Guardar el estado automáticamente cuando se carga una nueva imagen
                    // Esto asegura que la imagen se guarde en el servidor inmediatamente
                    guardarEstadoPlano(false);
                };
                reader.readAsDataURL(file);
            }
        });

        // Botón Guardar Estado
        btnGuardar.addEventListener('click', function() {
            guardarEstadoPlano(true);
        });

        // Botón Descargar Estado
        btnDescargar.addEventListener('click', () => {
            // Crear una copia del estado actual
            const estado = {
                imagen: planoImage.src, // Mantener la URL de la imagen
                trampas: window.puntos || [],
                zonas: window.zonas || []
            };
            
            // Para la descarga, podemos mantener la URL de la imagen
            // ya que el usuario puede necesitar acceder a ella más tarde
            const blob = new Blob([JSON.stringify(estado)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'estado-plano.json';
            a.click();
        });

        // Función para mostrar mensajes
        function mostrarMensaje(mensaje, tipo) {
            // Crear elemento de alerta
            const alerta = document.createElement('div');
            alerta.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg ${tipo === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
            alerta.textContent = mensaje;
            
            // Agregar al DOM
            document.body.appendChild(alerta);
            
            // Eliminar después de 3 segundos
            setTimeout(() => {
                alerta.style.opacity = '0';
                alerta.style.transition = 'opacity 0.5s';
                setTimeout(() => alerta.remove(), 500);
            }, 3000);
        }

        // Función para crear una zona existente desde datos guardados
        function crearZonaExistente(zonaData) {
            const planoContainer = document.getElementById('planoContainer');
            const planoImage = document.getElementById('planoImage');
            
            // Obtener las dimensiones actuales
            const imagenRect = planoImage.getBoundingClientRect();
            const containerRect = planoContainer.getBoundingClientRect();
            
            // Calcular la posición relativa al contenedor
            const imagenLeft = imagenRect.left - containerRect.left;
            const imagenTop = imagenRect.top - containerRect.top;
            
            // Manejar zonas de tipo polígono
            if (zonaData.tipo === 'poligono' && zonaData.puntos && Array.isArray(zonaData.puntos)) {
                // Crear el elemento del polígono
                const poligono = document.createElement('div');
                poligono.className = 'zona-poligono';
                
                // Calcular el path del polígono - las coordenadas ya están relativas a la imagen
                // Necesitamos convertirlas a coordenadas absolutas para visualización
                const path = zonaData.puntos.map(p => `${p.x + imagenLeft}px ${p.y + imagenTop}px`).join(',');
                poligono.style.clipPath = `polygon(${path})`;
                
                // Crear el contenedor del texto
                const textoZona = document.createElement('div');
                textoZona.className = 'zona-texto';
                textoZona.textContent = zonaData.nombre || 'Sin nombre';
                
                // Posicionar el texto en el centro de la zona
                if (zonaData.centro) {
                    // Las coordenadas del centro ya están relativas a la imagen
                    // Necesitamos convertirlas a coordenadas absolutas para visualización
                    textoZona.style.left = `${imagenLeft + zonaData.centro.x}px`;
                    textoZona.style.top = `${imagenTop + zonaData.centro.y}px`;
                } else {
                    // Calcular el centro si no está definido
                    const centroX = zonaData.puntos.reduce((sum, p) => sum + p.x, 0) / zonaData.puntos.length;
                    const centroY = zonaData.puntos.reduce((sum, p) => sum + p.y, 0) / zonaData.puntos.length;
                    textoZona.style.left = `${imagenLeft + centroX}px`;
                    textoZona.style.top = `${imagenTop + centroY}px`;
                }
                
                // Agregar ID al elemento del DOM
                poligono.dataset.zonaId = zonaData.id || `Z${window.zonas.indexOf(zonaData) + 1}`;
                poligono.dataset.index = window.zonas.indexOf(zonaData);
                
                // Agregar manejador para eliminar con clic derecho
                poligono.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    if (confirm('¿Desea eliminar esta zona?')) {
                        const index = parseInt(this.dataset.index);
                        window.zonas.splice(index, 1);
                        this.remove();
                        textoZona.remove();
                        reindexarZonas();
                    }
                });
                
                // Agregar al contenedor
                planoContainer.appendChild(poligono);
                planoContainer.appendChild(textoZona);
                
                return poligono;
            }
            
            // Código existente para zonas rectangulares o circulares
            const zonaElement = document.createElement('div');
            zonaElement.className = 'zona';
            if (zonaData.tipo === 'circulo') {
                zonaElement.classList.add('zona-circulo');
            }
            
            // Posicionar la zona - las coordenadas ya están relativas a la imagen
            zonaElement.style.left = `${imagenLeft + zonaData.x}px`;
            zonaElement.style.top = `${imagenTop + zonaData.y}px`;
            zonaElement.style.width = `${zonaData.width}px`;
            zonaElement.style.height = `${zonaData.height}px`;
            zonaElement.dataset.index = window.zonas.indexOf(zonaData);
            
            // Agregar nombre si existe
            if (zonaData.nombre) {
                const nombreElement = document.createElement('div');
                nombreElement.className = 'absolute top-0 left-0 bg-white px-2 py-1 text-sm font-semibold';
                nombreElement.textContent = zonaData.nombre;
                zonaElement.appendChild(nombreElement);
            }
            
            // Agregar manejador para eliminar con clic derecho
            zonaElement.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                if (confirm('¿Desea eliminar esta zona?')) {
                    const index = parseInt(this.dataset.index);
                    window.zonas.splice(index, 1);
                    this.remove();
                    reindexarZonas();
                }
            });
            
            // Agregar al contenedor
            planoContainer.appendChild(zonaElement);
            
            // Agregar controlador de redimensionamiento
            const resizeHandle = document.createElement('div');
            resizeHandle.className = 'resize-handle';
            resizeHandle.style.bottom = '0';
            resizeHandle.style.right = '0';
            zonaElement.appendChild(resizeHandle);
            
            return zonaElement;
        }

        // Función para reindexar las zonas
        function reindexarZonas() {
            // Reindexar zonas rectangulares y circulares
            const zonaElements = document.querySelectorAll('.zona');
            zonaElements.forEach((zona, index) => {
                zona.dataset.index = index;
            });
            
            // Reindexar zonas de tipo polígono
            const poligonoElements = document.querySelectorAll('.zona-poligono');
            poligonoElements.forEach((poligono, index) => {
                const zonaIndex = window.zonas.findIndex(z => z.id === poligono.dataset.zonaId);
                if (zonaIndex !== -1) {
                    poligono.dataset.index = zonaIndex;
                }
            });
        }

        // Función para limpiar completamente el estado
        function limpiarEstadoCompleto() {
            // Limpiar imagen
            if (planoImage) {
                planoImage.src = '';
                planoImage.style.display = 'none';
            }

            const placeholderText = document.getElementById('placeholderText');
            if (placeholderText) {
                placeholderText.style.display = 'block';
            }
            
            // Limpiar inputs
            if (planoInput) planoInput.value = '';
            if (cargarEstadoInput) cargarEstadoInput.value = '';
            
            // Limpiar arrays
            window.puntos = [];
            window.zonas = [];
            
            // Limpiar marcadores y zonas del DOM
            if (planoContainer) {
                const marcasExistentes = planoContainer.querySelectorAll('.trap-marker, .zona, .zona-poligono, .zona-texto, .punto-poligono');
                marcasExistentes.forEach(marca => marca.remove());
            }
            
            // Limpiar tabla
            if (typeof actualizarTablaTrampas === 'function') {
                actualizarTablaTrampas();
            }
            
            // Resetear modos de edición
            if (planoContainer) {
                planoContainer.dataset.modoEdicion = '';
                planoContainer.dataset.tipoTrampa = '';
            }
            
            // Resetear clases activas de botones
            if (btnMoverTrampa) {
                btnMoverTrampa.classList.remove('active', 'bg-green-600', 'hover:bg-green-700');
                btnMoverTrampa.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
                btnMoverTrampa.innerHTML = `
                    <i class="fas fa-arrows-alt mr-2"></i>
                    Mover Trampas
                `;
            }

            // Verificar que el dropdown y su menú existan antes de manipularlos
            const trapMenu = dropdownTrampa?.querySelector('.trap-menu');
            if (trapMenu) {
                trapMenu.classList.add('hidden');
            }

            // Desactivar botones después de limpiar
            desactivarBotones();
        }

        // Botón Limpiar Todo
        btnLimpiar.addEventListener('click', () => {
            if (confirm('¿Está seguro de que desea limpiar todo?')) {
                limpiarEstadoCompleto();
            }
        });

        // Botón Cargar Estado
        btnCargar.addEventListener('click', () => {
            cargarEstadoInput.click();
        });

        // Evento para cargar estado desde archivo
        cargarEstadoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const estado = JSON.parse(e.target.result);
                    
                    // Limpiar el estado actual
                    limpiarEstadoCompleto();
                    
                    // Verificar si la imagen es una URL o datos base64
                    if (estado.imagen) {
                        // Crear una nueva imagen para asegurar que se dispare el evento onload
                        const img = new Image();
                        img.onload = function() {
                            planoImage.src = img.src;
                            planoImage.style.display = 'block';
                            document.getElementById('placeholderText').style.display = 'none';
                            
                            // Cargar trampas después de que la imagen esté cargada
                            if (estado.trampas && Array.isArray(estado.trampas)) {
                                window.puntos = estado.trampas;
                                
                                // Crear una función para colocar las trampas después de que la imagen esté completamente cargada
                                const colocarTrampas = function() {
                                    // Limpiar marcadores existentes para evitar duplicados
                                    document.querySelectorAll('.trap-marker').forEach(marker => marker.remove());
                                    
                                    // Colocar las trampas
                                    window.puntos.forEach(punto => {
                                        marcarTrampa(punto);
                                    });
                                    
                                    // Actualizar la tabla
                                    actualizarTablaTrampas();
                                };
                                
                                // Si la imagen ya está cargada, colocar las trampas inmediatamente
                                if (planoImage.complete) {
                                    colocarTrampas();
                                } else {
                                    // Si la imagen aún no está cargada, esperar a que se cargue
                                    planoImage.addEventListener('load', colocarTrampas);
                                }
                            }
                            
                            // Cargar zonas después de que la imagen esté cargada
                            if (estado.zonas && Array.isArray(estado.zonas)) {
                                window.zonas = estado.zonas;
                                
                                // Crear una función para colocar las zonas después de que la imagen esté completamente cargada
                                const colocarZonas = function() {
                                    // Limpiar zonas existentes para evitar duplicados
                                    document.querySelectorAll('.zona, .zona-poligono, .zona-texto').forEach(zona => zona.remove());
                                    
                                    // Colocar las zonas
                                    window.zonas.forEach(zona => {
                                        crearZonaExistente(zona);
                                    });
                                };
                                
                                // Si la imagen ya está cargada, colocar las zonas inmediatamente
                                if (planoImage.complete) {
                                    colocarZonas();
                                } else {
                                    // Si la imagen aún no está cargada, esperar a que se cargue
                                    planoImage.addEventListener('load', colocarZonas);
                                }
                            }
                            
                            // Activar botones
                            activarBotones();
                            
                            // Guardar el estado automáticamente para asegurar que se guarde la imagen en el servidor
                            guardarEstadoPlano(false);
                        };
                        
                        img.src = estado.imagen;
                    } else {
                        mostrarMensaje('El archivo JSON no contiene una imagen válida.', 'error');
                    }
                } catch (error) {
                    console.error('Error al cargar el estado:', error);
                    mostrarMensaje('Error al cargar el archivo. Asegúrate de que sea un archivo JSON válido.', 'error');
                }
            };
            reader.readAsText(file);
        });

        // Función para marcar una trampa en el plano
        function marcarTrampa(punto) {
            const planoContainer = document.getElementById('planoContainer');
            const planoImage = document.getElementById('planoImage');
            
            // Verificar que la imagen esté cargada
            if (!planoImage.complete || !planoImage.naturalWidth) {
                console.warn('La imagen no está completamente cargada. Reintentando en 100ms...');
                setTimeout(() => marcarTrampa(punto), 100);
                return;
            }
            
            // Obtener las dimensiones actuales
            const imagenRect = planoImage.getBoundingClientRect();
            const containerRect = planoContainer.getBoundingClientRect();

            // Crear el marcador visual
            const marcador = document.createElement('div');
            marcador.className = 'trap-marker';
            marcador.style.position = 'absolute';
            
            // Guardar las coordenadas originales como atributos de datos
            marcador.dataset.originalX = punto.x;
            marcador.dataset.originalY = punto.y;
            
            // Calcular la posición relativa al contenedor
            const imagenLeft = imagenRect.left - containerRect.left;
            const imagenTop = imagenRect.top - containerRect.top;
            
            // Posicionar el marcador
            marcador.style.left = `${imagenLeft + punto.x}px`;
            marcador.style.top = `${imagenTop + punto.y}px`;
            marcador.style.transform = 'translate(-50%, -50%)';
            marcador.dataset.index = window.puntos.indexOf(punto);

            // Agregar icono según el tipo
            const icon = document.createElement('i');
            switch (punto.tipo) {
                case 'edc_quimicas':
                    icon.className = 'fas fa-flask';
                    break;
                case 'luz_uv':
                    icon.className = 'fas fa-lightbulb';
                    break;
                case 'feromona_gorgojo':
                    icon.className = 'fas fa-bug';
                    break;
                case 'equipo_sonico':
                    icon.className = 'fas fa-volume-up';
                    break;
                case 'globo_terror':
                    icon.className = 'fas fa-circle';
                    break;
                case 'atrayente_chinches':
                    icon.className = 'fas fa-spider';
                    break;
                case 'atrayente_pulgas':
                    icon.className = 'fas fa-paw';
                    break;
                case 'feromona_picudo':
                    icon.className = 'fas fa-seedling';
                    icon.style.color = '#dc2626'; // Color rojo
                    break;
                // Mantener compatibilidad con tipos antiguos
                case 'rodent':
                    icon.className = 'fas fa-mouse';
                    break;
                case 'insect':
                    icon.className = 'fas fa-bug';
                    break;
                case 'fly':
                    icon.className = 'fas fa-fly';
                    break;
                default:
                    icon.className = 'fas fa-trap';
                    break;
            }
            marcador.appendChild(icon);

            // Agregar tooltip con información
            marcador.title = `${getTipoTrampa(punto.tipo)} - ${punto.zona || 'Sin zona'} (ID: ${punto.id.startsWith('TEMP-') ? 'Nueva' : punto.id})`;
            
            // Agregar ID visible en el marcador
            const idLabel = document.createElement('span');
            idLabel.className = 'trap-id-label';
            // No mostrar el ID temporal con prefijo TEMP-, mostrar "Nueva" en su lugar
            idLabel.textContent = punto.id.startsWith('TEMP-') ? 'Nueva' : punto.id;
            idLabel.style.position = 'absolute';
            idLabel.style.top = '-20px';
            idLabel.style.left = '50%';
            idLabel.style.transform = 'translateX(-50%)';
            idLabel.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
            idLabel.style.color = 'white';
            idLabel.style.padding = '2px 5px';
            idLabel.style.borderRadius = '3px';
            idLabel.style.fontSize = '9px';
            idLabel.style.whiteSpace = 'nowrap';
            idLabel.style.maxWidth = '120px';
            idLabel.style.overflow = 'hidden';
            idLabel.style.textOverflow = 'ellipsis';
            marcador.appendChild(idLabel);

            // Agregar evento para eliminar
            marcador.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                if (confirm('¿Desea eliminar esta trampa?')) {
                    const index = parseInt(this.dataset.index);
                    window.puntos.splice(index, 1);
                    this.remove();
                    reindexarTrampas();
                    actualizarTablaTrampas();
                    
                    // Guardar automáticamente el estado del plano después de eliminar la trampa
                    guardarEstadoPlano(false);
                }
            });

            // Agregar evento de clic al marcador
            const marcadorConEvento = addTrapClickEvent(marcador);

            planoContainer.appendChild(marcadorConEvento);
            return marcadorConEvento;
        }

        // Modificar el evento de clic en las trampas
        function addTrapClickEvent(marker) {
            // Remove existing click event to avoid duplicates
            const newMarker = marker.cloneNode(true);
            if (marker.parentNode) {
                marker.parentNode.replaceChild(newMarker, marker);
            }
            
            // Add the click event to the new marker
            newMarker.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Si ya hay un tooltip visible, ocultarlo
                if (tooltip.style.display === 'block' && activeMarker === this) {
                    hideTooltip();
                    return;
                }

                // Ocultar cualquier otro tooltip visible
                hideTooltip();
                
                // Mostrar el tooltip para esta trampa
                showTooltip(this, e);
                
                // Resaltar la trampa seleccionada
                document.querySelectorAll('.trap-marker').forEach(m => {
                    m.classList.remove('highlighted');
                });
                this.classList.add('highlighted');
            });
            
            return newMarker;
        }

        // Función para mostrar el tooltip
        function showTooltip(marker, event) {
            const rect = marker.getBoundingClientRect();
            
            // Asegurarse de que el tooltip tenga el contenido correcto
            tooltip.innerHTML = `
                <button type="button" class="add-incidence-btn">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Agregar incidencia
                </button>
            `;
            
            // Agregar evento al botón de incidencia
            tooltip.querySelector('.add-incidence-btn').addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (activeMarker) {
                    const trampaIndex = activeMarker.dataset.index;
                    const trampa = window.puntos[trampaIndex];
                    
                    if (!trampa) {
                        mostrarMensaje('Error: No se pudo identificar la trampa', 'error');
                        return;
                    }
                    
                    // Actualizar la información de la trampa en el modal
                    document.getElementById('trampa_id').value = trampa.id_trampa || trampa.id; // Usar id_trampa si existe, si no usar id
                    document.getElementById('trampaDbIdDisplay').textContent = trampa.id.startsWith('TEMP-') ? 'Pendiente' : (trampa.id || 'Sin ID');
                    document.getElementById('trampaZonaDisplay').textContent = trampa.zona || 'Sin zona';
                    
                    // Establecer la fecha y hora actual como valor predeterminado
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    document.getElementById('fecha_incidencia').value = `${year}-${month}-${day}T${hours}:${minutes}`;
                    
                    // Mostrar el modal
                    document.getElementById('modalIncidencia').classList.remove('hidden');
                    
                    // Asegurar que los marcadores estén por debajo del modal
                    document.querySelectorAll('.trap-marker').forEach(marker => {
                        marker.style.zIndex = '5';
                    });
                    
                    hideTooltip();
                }
            });
            
            // Mostrar el tooltip
            tooltip.style.display = 'block';
            tooltip.style.left = `${rect.left}px`;
            tooltip.style.top = `${rect.bottom + 5}px`;
            activeMarker = marker;
        }

        // Función para ocultar el tooltip
        function hideTooltip() {
            tooltip.style.display = 'none';
            activeMarker = null;
        }

        // Asignar la función marcarTrampa al objeto window
        window.marcarTrampa = marcarTrampa;

        // Agregar el evento a las trampas existentes
        document.querySelectorAll('.trap-marker').forEach(marker => {
            const newMarker = addTrapClickEvent(marker);
            if (marker !== newMarker && marker.parentNode) {
                marker.parentNode.replaceChild(newMarker, marker);
            }
        });

        // Cerrar tooltip al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.trap-marker') && !e.target.closest('.trap-tooltip')) {
                hideTooltip();
            }
        });

        // Reattach trap click events when page becomes visible again
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Recargar el estado del plano desde el servidor
                recargarEstadoPlano();
                
                // Page is now visible, reattach trap click events
                document.querySelectorAll('.trap-marker').forEach(marker => {
                    // Add the click event and get the new marker
                    const newMarker = addTrapClickEvent(marker);
                    // Replace the old marker with the new one
                    if (marker !== newMarker && marker.parentNode) {
                        marker.parentNode.replaceChild(newMarker, marker);
                    }
                });
                
                // Reposicionar las trampas
                reposicionarTrampas();
            }
        });

        // Función para recargar el estado del plano desde el servidor
        function recargarEstadoPlano() {
            fetch(`<?= base_url('blueprints/obtener_estado/' . $plano['id']) ?>`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.plano && data.plano.archivo) {
                    try {
                        const estadoGuardado = JSON.parse(data.plano.archivo);
                        if (estadoGuardado && estadoGuardado.trampas && Array.isArray(estadoGuardado.trampas)) {
                            // Actualizar el array de puntos con los datos del servidor
                            window.puntos = estadoGuardado.trampas;
                            
                            // Limpiar marcadores existentes para evitar duplicados
                            document.querySelectorAll('.trap-marker').forEach(marker => marker.remove());
                            
                            // Colocar las trampas
                            window.puntos.forEach(punto => {
                                marcarTrampa(punto);
                            });
                            
                            // Actualizar la tabla
                            actualizarTablaTrampas();
                            
                            // Asegurar que todos los marcadores tengan el evento de clic
                            document.querySelectorAll('.trap-marker').forEach(marker => {
                                const newMarker = addTrapClickEvent(marker);
                                if (marker !== newMarker && marker.parentNode) {
                                    marker.parentNode.replaceChild(newMarker, marker);
                                }
                            });
                            
                            // Reposicionar las trampas para asegurar que estén en la posición correcta
                            setTimeout(reposicionarTrampas, 100);
                        }
                    } catch (error) {
                        console.error('Error al procesar el estado del plano:', error);
                    }
                }
            })
            .catch(error => {
                console.error('Error al recargar el estado del plano:', error);
            });
        }

        // Handle page loads from cache (when navigating back)
        window.addEventListener('pageshow', function(event) {
            // If the page is loaded from cache (navigating back)
            if (event.persisted) {
                // Recargar el estado del plano desde el servidor
                recargarEstadoPlano();
                
                // Reattach trap click events
                document.querySelectorAll('.trap-marker').forEach(marker => {
                    // Add the click event and get the new marker
                    const newMarker = addTrapClickEvent(marker);
                    // Replace the old marker with the new one
                    if (marker !== newMarker && marker.parentNode) {
                        marker.parentNode.replaceChild(newMarker, marker);
                    }
                });
                
                // Reposicionar las trampas
                reposicionarTrampas();
            }
        });

        // Dropdown de Agregar Trampa
        const dropdownButton = dropdownTrampa.querySelector('button');
        const dropdownMenu = dropdownTrampa.querySelector('.trap-menu');

        dropdownButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        // Eventos para tipos de trampas
        const trapLinks = dropdownMenu.querySelectorAll('a[data-trap-type]');
        trapLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                tipoTrampaSeleccionado = e.target.closest('a').dataset.trapType;
                modoEdicion = 'agregarTrampa';
                dropdownMenu.classList.add('hidden');
                
                // Cambiar el cursor para indicar modo de agregar
                planoContainer.style.cursor = 'crosshair';
                
                // Agregar clase activa al botón
                dropdownButton.classList.add('active');
                dropdownButton.style.backgroundColor = '#2563eb'; // Azul más oscuro
            });
        });

        // Reemplazar el evento de clic en el plano
        planoContainer.addEventListener('click', function(e) {
            const rect = planoContainer.getBoundingClientRect();
            const imagen = planoImage.getBoundingClientRect();
            
            const clickX = e.clientX - rect.left;
            const clickY = e.clientY - rect.top;
            
            // Verificar si el clic está dentro de la imagen
            const imagenLeft = imagen.left - rect.left;
            const imagenTop = imagen.top - rect.top;
            const imagenRight = imagenLeft + imagen.width;
            const imagenBottom = imagenTop + imagen.height;
            
            if (clickX >= imagenLeft && clickX <= imagenRight && 
                clickY >= imagenTop && clickY <= imagenBottom) {
                
                // Manejar modo de dibujar zona
                if (planoContainer.dataset.modoEdicion === 'dibujarZona') {
                    // Agregar punto al polígono - guardar coordenadas relativas a la imagen
                    puntosPoligono.push({ 
                        x: clickX - imagenLeft, 
                        y: clickY - imagenTop 
                    });
                    
                    // Crear marcador de punto - mostrar en coordenadas absolutas
                    const punto = document.createElement('div');
                    punto.className = 'punto-poligono';
                    punto.style.left = `${clickX}px`;
                    punto.style.top = `${clickY}px`;
                    planoContainer.appendChild(punto);
                    
                    // Actualizar polígono temporal - usar coordenadas absolutas para visualización
                    if (puntosPoligono.length > 2) {
                        const path = puntosPoligono.map(p => `${p.x + imagenLeft}px ${p.y + imagenTop}px`).join(',');
                        poligonoTemporal.style.clipPath = `polygon(${path})`;
                        poligonoTemporal.style.display = 'block';
                    }

                    // Actualizar tooltip y mostrar botón de confirmar
                    if (puntosPoligono.length >= 3) {
                        tooltipElement.innerHTML = 'Haga clic en "Confirmar Zona" para finalizar';
                        confirmButton.classList.remove('hidden');
                    } else {
                        tooltipElement.innerHTML = `Haga clic para agregar puntos. Faltan ${3 - puntosPoligono.length} puntos mínimos.`;
                    }
                }
                
                // Manejar modo de agregar trampa
                else if (modoEdicion === 'agregarTrampa' && tipoTrampaSeleccionado) {
                    // Ya no solicitamos el nombre de la trampa
                    
                    // Solicitar la zona
                    const zona = prompt('Ingrese la zona donde se ubicará la trampa:', '');
                    
                    // Generar un ID temporal
                    const tempId = `TEMP-${Date.now().toString().slice(-6)}`;
                    
                    // Crear nueva trampa con zona
                    const nuevaTrampa = {
                        id: tempId, // ID temporal que será reemplazado por el generado en el servidor
                        tipo: tipoTrampaSeleccionado,
                        x: clickX - imagenLeft,
                        y: clickY - imagenTop,
                        zona: zona || 'Sin zona' // Usar 'Sin zona' si no se proporciona una
                    };

                    // Agregar al array de puntos
                    if (!window.puntos) window.puntos = [];
                    window.puntos.push(nuevaTrampa);

                    // Crear el marcador visual
                    const marcador = marcarTrampa(nuevaTrampa);
                    actualizarTablaTrampas();
                    
                    // Guardar en la base de datos
                    guardarTrampaEnBD(nuevaTrampa);
                    
                    // Desactivar modo de agregar trampa después de colocar una
                    modoEdicion = null;
                    tipoTrampaSeleccionado = null;
                    planoContainer.style.cursor = 'default';
                    
                    // Resetear estado visual del botón
                    dropdownButton.classList.remove('active');
                    dropdownButton.style.backgroundColor = '';
                }
            }
        });

        // Cerrar dropdown cuando se hace clic fuera
        document.addEventListener('click', (e) => {
            if (!dropdownTrampa.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });

        // Función para cerrar el modal de incidencia
        window.closeIncidenciaModal = function() {
            document.getElementById('modalIncidencia').classList.add('hidden');
            // Restaurar la visibilidad de los marcadores
            document.querySelectorAll('.trap-marker').forEach(marker => {
                marker.style.zIndex = '10';
            });
            
            // Reiniciar el formulario
            document.getElementById('formIncidencia').reset();
            
            // Ocultar campos adicionales
            document.getElementById('tipo_plaga_personalizado_container').style.display = 'none';
            document.getElementById('cantidad_organismos_container').style.display = 'none';
            
            // Quitar atributos required
            document.getElementById('tipo_plaga_personalizado').removeAttribute('required');
            document.getElementById('cantidad_organismos').removeAttribute('required');
        }

        // Manejar envío del formulario de incidencia
        document.getElementById('formIncidencia').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Obtener los datos del formulario
            const formData = new FormData(this);
            
            // Verificar que se haya seleccionado un tipo de plaga
            if (!formData.get('tipo_plaga')) {
                mostrarMensaje('Debe seleccionar un tipo de plaga', 'error');
                return;
            }
            
            // El trampa_id ya contiene el ID real de la trampa, no necesitamos convertirlo
            const trampaId = formData.get('trampa_id');
            
            // Mostrar información de depuración
            console.log('Todos los puntos disponibles:', window.puntos);
            console.log('Buscando trampa con ID:', trampaId);
            
            // Buscar la trampa por su ID para mostrar información
            const trampa = window.puntos.find(p => p.id_trampa === trampaId || p.id === trampaId);
            if (!trampa) {
                mostrarMensaje('Error: No se pudo identificar la trampa', 'error');
                return;
            }
            
            console.log('Enviando incidencia para trampa:', trampa);
            console.log('ID de trampa enviado:', trampaId);
            
            // Enviar los datos al servidor
            fetch('<?= base_url('blueprints/guardar_incidencia') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Obtener la trampa para la que se registró la incidencia
                    const trampaId = document.getElementById('trampa_id').value;
                    const trampa = window.puntos.find(p => p.id === trampaId);
                    
                    // Mostrar mensaje de éxito con detalles
                    const mensaje = `Incidencia registrada correctamente para la trampa ${trampaId} (${trampa ? trampa.nombre || 'Sin nombre' : 'Desconocida'})`;
                    mostrarMensaje(mensaje, 'success');
                    
                    // Cerrar el modal y limpiar el formulario
                    closeIncidenciaModal();
                    
                    // Resaltar la trampa para la que se registró la incidencia
                    const marker = document.querySelector(`.trap-marker[data-index="${window.puntos.findIndex(p => p.id === trampaId)}"]`);
                    if (marker) {
                        document.querySelectorAll('.trap-marker').forEach(m => {
                            m.classList.remove('highlighted');
                        });
                        marker.classList.add('highlighted');
                        
                        // Agregar una animación para indicar que se registró una incidencia
                        marker.style.animation = 'pulse 1s';
                        setTimeout(() => {
                            marker.style.animation = '';
                        }, 1000);
                    }
                } else {
                    mostrarMensaje(`Error: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                console.error('Error al guardar incidencia:', error);
                mostrarMensaje(`Error al guardar incidencia: ${error.message}`, 'error');
            });
        });

        // Mostrar/ocultar campo de cantidad de organismos cuando se selecciona un tipo de plaga
        document.getElementById('tipo_plaga_select').addEventListener('change', function() {
            const tipoPlagaPersonalizadoContainer = document.getElementById('tipo_plaga_personalizado_container');
            const cantidadContainer = document.getElementById('cantidad_organismos_container');
            const tipoPlagaHidden = document.getElementById('tipo_plaga');
            
            // Actualizar el campo oculto con el valor seleccionado
            tipoPlagaHidden.value = this.value;
            
            // Mostrar/ocultar el campo personalizado si se selecciona "otro"
            if (this.value === 'otro') {
                tipoPlagaPersonalizadoContainer.style.display = 'block';
                document.getElementById('tipo_plaga_personalizado').setAttribute('required', 'required');
                // Limpiar el campo oculto para que se actualice con el valor personalizado
                tipoPlagaHidden.value = '';
            } else {
                tipoPlagaPersonalizadoContainer.style.display = 'none';
                document.getElementById('tipo_plaga_personalizado').removeAttribute('required');
            }
            
            // Mostrar/ocultar el campo de cantidad de organismos
            if (this.value) {
                cantidadContainer.style.display = 'block';
                document.getElementById('cantidad_organismos').setAttribute('required', 'required');
            } else {
                cantidadContainer.style.display = 'none';
                document.getElementById('cantidad_organismos').removeAttribute('required');
            }
        });
        
        // Actualizar el campo oculto cuando se escribe en el campo personalizado
        document.getElementById('tipo_plaga_personalizado').addEventListener('input', function() {
            document.getElementById('tipo_plaga').value = this.value;
        });

        // Agregar esta función para reindexar las trampas
        function reindexarTrampas() {
            // Reindexar los marcadores en el DOM
            document.querySelectorAll('.trap-marker').forEach((marker, index) => {
                marker.dataset.index = index;
            });
        }

        // Modificar la función de eliminar en actualizarTablaTrampas
        function actualizarTablaTrampas() {
            const tbody = document.getElementById('trampasTableBody');
            if (!tbody) return;
            
            tbody.innerHTML = '';

            if (!window.puntos || window.puntos.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                            No hay trampas registradas
                        </td>
                    </tr>`;
                return;
            }

            // Modificar el encabezado de la tabla para eliminar la columna de nombre
            const thead = document.querySelector('table thead tr');
            if (thead) {
                thead.innerHTML = `
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Tipo</th>
                    <th class="px-4 py-2 text-left">Ubicación</th>
                    <th class="px-4 py-2 text-left">Zona</th>
                    <th class="px-4 py-2 text-right">Acciones</th>
                `;
            }

            window.puntos.forEach((punto, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 cursor-pointer';
                tr.innerHTML = `
                    <td class="px-4 py-2">${punto.id && punto.id.startsWith('TEMP-') ? 'Nueva' : (punto.id || `T${index + 1}`)}</td>
                    <td class="px-4 py-2">${getTipoTrampa(punto.tipo)}</td>
                    <td class="px-4 py-2">(${Math.round(punto.x)}, ${Math.round(punto.y)})</td>
                    <td class="px-4 py-2">${punto.zona || 'Sin zona'}</td>
                    <td class="px-4 py-2 text-right">
                        <button class="text-red-600 hover:text-red-800 delete-trap" title="Eliminar trampa">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                `;
                
                // Agregar evento de clic a la fila
                tr.addEventListener('click', (e) => {
                    if (e.target.closest('.delete-trap')) return;
                    
                    document.querySelectorAll('.trap-marker').forEach(marker => {
                        marker.classList.remove('highlighted');
                    });
                    
                    tbody.querySelectorAll('tr').forEach(row => {
                        row.classList.remove('selected');
                    });
                    
                    const marker = document.querySelector(`.trap-marker[data-index="${index}"]`);
                    if (marker) {
                        marker.classList.add('highlighted');
                        marker.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    tr.classList.add('selected');
                });

                // Modificar el evento del botón eliminar
                const deleteBtn = tr.querySelector('.delete-trap');
                deleteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (confirm('¿Está seguro de que desea eliminar esta trampa?')) {
                        // Eliminar el marcador del plano
                        const marker = document.querySelector(`.trap-marker[data-index="${index}"]`);
                        if (marker) marker.remove();
                        
                        // Eliminar del array de puntos
                        window.puntos.splice(index, 1);
                        
                        // Reindexar las trampas restantes
                        reindexarTrampas();
                        
                        // Actualizar la tabla
                        actualizarTablaTrampas();
                        
                        // Guardar automáticamente el estado del plano después de eliminar la trampa
                        guardarEstadoPlano(false);
                    }
                });
                
                tbody.appendChild(tr);
            });
        }

        // Función auxiliar para obtener el nombre del tipo de trampa
        function getTipoTrampa(tipo) {
            const tipos = {
                'edc_quimicas': 'EDC Químicas',
                'luz_uv': 'Equipo de Luz UV',
                'feromona_gorgojo': 'Trampa de Feromona Gorgojo',
                'equipo_sonico': 'Equipo Sónico',
                'globo_terror': 'Globo terror',
                'atrayente_chinches': 'Trampa atrayente chinches',
                'atrayente_pulgas': 'Trampa atrayente pulgas',
                'feromona_picudo': 'Trampa feromonas picudo rojo',
                // Mantener compatibilidad con tipos antiguos
                'rodent': 'Trampa para Roedores',
                'insect': 'Trampa para Insectos',
                'fly': 'Trampa para Moscas',
                'moth': 'Trampa para Polillas'
            };
            return tipos[tipo] || 'Desconocido';
        }

        // Agregar estilos CSS para los marcadores
        const style = document.createElement('style');
        style.textContent = `
            .trap-marker {
                cursor: pointer;
                padding: 8px;
                background: rgba(255, 255, 255, 0.9);
                border-radius: 50%;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                z-index: 10;
                transition: transform 0.2s;
            }
            .trap-marker:hover {
                transform: scale(1.1);
                background: rgba(255, 255, 255, 1);
            }
            .trap-marker i {
                font-size: 16px;
                color: #444;
            }
            
            /* Cuando el modal está abierto, asegurarse de que los marcadores estén por debajo */
            #modalIncidencia:not(.hidden) ~ * .trap-marker {
                z-index: 5 !important;
            }
        `;
        document.head.appendChild(style);

        // Agregar estilos para el contenedor del plano
        const planoContainerStyle = document.createElement('style');
        planoContainerStyle.textContent = `
            #planoContainer {
                position: relative;
                overflow: hidden;
            }
            #planoImage {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }
        `;
        document.head.appendChild(planoContainerStyle);

        // Agregar estos estilos CSS
        const moveStyles = document.createElement('style');
        moveStyles.textContent = `
            .trap-marker.movable {
                cursor: move;
            }
            .trap-marker.moving {
                opacity: 0.8;
                transform: scale(1.1);
            }
        `;
        document.head.appendChild(moveStyles);

        // Agregar estos estilos CSS para el botón activo
        const dropdownStyles = document.createElement('style');
        dropdownStyles.textContent = `
            .dropdown button.active {
                background-color: #2563eb !important;
            }
            .dropdown button.active:hover {
                background-color: #1d4ed8 !important;
            }
        `;
        document.head.appendChild(dropdownStyles);

        // Agregar estos estilos CSS para el resaltado
        const highlightStyles = document.createElement('style');
        highlightStyles.textContent = `
            .trap-marker.highlighted {
                box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.7);
                transform: scale(1.2);
                z-index: 1000;
            }
        `;
        document.head.appendChild(highlightStyles);

        // Agregar el evento para el botón de Agregar Zona
        btnAgregarZona.addEventListener('click', () => {
            const modoActual = planoContainer.dataset.modoEdicion || '';
            if (modoActual === 'dibujarZona') {
                // Desactivar modo dibujar
                finalizarDibujoPoligono();
                planoContainer.dataset.modoEdicion = '';
                btnAgregarZona.classList.remove('active');
                planoContainer.style.cursor = 'default';
            } else {
                // Activar modo dibujar
                planoContainer.dataset.modoEdicion = 'dibujarZona';
                btnAgregarZona.classList.add('active');
                planoContainer.style.cursor = 'crosshair';
                iniciarDibujoPoligono();
            }
        });

        // Función para iniciar el dibujo del polígono
        function iniciarDibujoPoligono() {
            dibujandoPoligono = true;
            puntosPoligono = [];
            
            // Crear el polígono temporal
            poligonoTemporal = document.createElement('div');
            poligonoTemporal.className = 'zona-poligono-temporal';
            planoContainer.appendChild(poligonoTemporal);

            // Crear tooltip flotante
            tooltipElement = document.createElement('div');
            tooltipElement.className = 'tooltip-flotante';
            tooltipElement.innerHTML = 'Haga clic para agregar puntos. Mínimo 3 puntos.';
            document.body.appendChild(tooltipElement);

            // Crear botón de confirmar
            confirmButton = document.createElement('button');
            confirmButton.className = 'confirm-polygon-button hidden';
            confirmButton.innerHTML = `
                <i class="fas fa-check mr-2"></i>
                Confirmar Zona
            `;
            planoContainer.appendChild(confirmButton);

            // Evento para el botón confirmar
            confirmButton.addEventListener('click', () => {
                if (puntosPoligono.length >= 3) {
                    finalizarDibujoPoligono();
                    planoContainer.dataset.modoEdicion = '';
                    btnAgregarZona.classList.remove('active');
                    planoContainer.style.cursor = 'default';
                }
            });

            // Actualizar posición del tooltip con el movimiento del mouse
            document.addEventListener('mousemove', actualizarPosicionTooltip);
        }

        // Función para actualizar la posición del tooltip
        function actualizarPosicionTooltip(e) {
            if (tooltipElement) {
                const offset = 5; // Distancia del cursor al tooltip
                tooltipElement.style.left = `${e.clientX + offset}px`;
                tooltipElement.style.top = `${e.clientY + offset}px`;
            }
        }

        // Función para finalizar el dibujo
        function finalizarDibujoPoligono() {
            if (puntosPoligono.length >= 3) {
                // Solicitar nombre de la zona
                const nombreZona = prompt('Ingrese un nombre para la zona:', '');
                if (nombreZona) {
                    // Obtener las dimensiones actuales
                    const imagenRect = planoImage.getBoundingClientRect();
                    const containerRect = planoContainer.getBoundingClientRect();
                    
                    // Calcular la posición relativa al contenedor
                    const imagenLeft = imagenRect.left - containerRect.left;
                    const imagenTop = imagenRect.top - containerRect.top;
                    
                    // Crear el polígono final
                    const poligono = document.createElement('div');
                    poligono.className = 'zona-poligono';
                    
                    // Calcular el path del polígono - usar coordenadas absolutas para visualización
                    const path = puntosPoligono.map(p => `${p.x + imagenLeft}px ${p.y + imagenTop}px`).join(',');
                    poligono.style.clipPath = `polygon(${path})`;
                    
                    // Crear el contenedor del texto
                    const textoZona = document.createElement('div');
                    textoZona.className = 'zona-texto';
                    textoZona.textContent = nombreZona;
                    
                    // Calcular el centro de la zona para posicionar el texto
                    const centroX = puntosPoligono.reduce((sum, p) => sum + p.x, 0) / puntosPoligono.length;
                    const centroY = puntosPoligono.reduce((sum, p) => sum + p.y, 0) / puntosPoligono.length;
                    
                    // Posicionar el texto - usar coordenadas absolutas para visualización
                    textoZona.style.left = `${centroX + imagenLeft}px`;
                    textoZona.style.top = `${centroY + imagenTop}px`;
                    
                    // Guardar la zona - guardar coordenadas relativas a la imagen
                    if (!window.zonas) window.zonas = [];
                    const zonaId = `Z${window.zonas.length + 1}`;
                    window.zonas.push({
                        tipo: 'poligono',
                        puntos: puntosPoligono, // Ya están en coordenadas relativas a la imagen
                        id: zonaId,
                        nombre: nombreZona,
                        centro: { x: centroX, y: centroY } // Guardar centro relativo a la imagen
                    });
                    
                    // Agregar ID al elemento del DOM
                    poligono.dataset.zonaId = zonaId;
                    poligono.dataset.index = window.zonas.length - 1;
                    planoContainer.appendChild(poligono);
                    planoContainer.appendChild(textoZona);
                }
            }
            
            // Limpiar
            dibujandoPoligono = false;
            puntosPoligono = [];
            
            if (poligonoTemporal) {
                poligonoTemporal.remove();
                poligonoTemporal = null;
            }
            if (tooltipElement) {
                tooltipElement.remove();
                tooltipElement = null;
                document.removeEventListener('mousemove', actualizarPosicionTooltip);
            }
            if (confirmButton) {
                confirmButton.remove();
                confirmButton = null;
            }
            document.querySelectorAll('.punto-poligono').forEach(punto => punto.remove());
        }

        // Agregar estos estilos para el texto de la zona
        const zonaStyles = document.createElement('style');
        zonaStyles.textContent = `
            .tooltip-flotante {
                position: fixed;
                background-color: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 14px;
                pointer-events: none;
                z-index: 1000;
                transform: translate(0, -100%);
                white-space: nowrap;
            }

            .confirm-polygon-button {
                position: absolute;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #059669;
                color: white;
                padding: 8px 16px;
                border-radius: 6px;
                cursor: pointer;
                display: flex;
                align-items: center;
                transition: all 0.2s;
                z-index: 1000;
            }

            .confirm-polygon-button:hover {
                background-color: #047857;
            }

            .confirm-polygon-button.hidden {
                display: none;
            }

            .zona-poligono-temporal,
            .zona-poligono {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(255, 255, 0, 0.2);
                border: 2px dashed rgba(0, 0, 0, 0.5);
                pointer-events: none;
            }

            .punto-poligono {
                position: absolute;
                width: 8px;
                height: 8px;
                background-color: #ff0;
                border: 1px solid #000;
                border-radius: 50%;
                transform: translate(-50%, -50%);
                z-index: 20;
            }

            .zona-texto {
                position: absolute;
                background: rgba(255, 255, 255, 0.8);
                padding: 2px 6px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: bold;
                transform: translate(-50%, -50%);
                z-index: 15;
                pointer-events: none;
                white-space: nowrap;
            }
        `;
        document.head.appendChild(zonaStyles);

        // Agregar estilos para botones desactivados
        const buttonStyles = document.createElement('style');
        buttonStyles.textContent = `
            button:disabled {
                pointer-events: none;
            }
            
            .cursor-not-allowed {
                cursor: not-allowed !important;
            }
            
            /* Estilos para el modal de incidencias */
            #modalIncidencia {
                z-index: 9999 !important; /* Asegurar que esté por encima de todo */
            }
            
            #modalIncidencia .bg-white {
                position: relative;
                z-index: 10000; /* Asegurar que el contenido del modal esté por encima del fondo */
            }
        `;
        document.head.appendChild(buttonStyles);

        // Botón Mover Trampas
        btnMoverTrampa.addEventListener('click', () => {
            const modoActual = planoContainer.dataset.modoEdicion || '';
            if (modoActual === 'mover') {
                // Desactivar modo mover
                planoContainer.dataset.modoEdicion = '';
                btnMoverTrampa.classList.remove('active', 'bg-green-600', 'hover:bg-green-700');
                btnMoverTrampa.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
                btnMoverTrampa.innerHTML = `
                    <i class="fas fa-arrows-alt mr-2"></i>
                    Mover Trampas
                `;
                planoContainer.style.cursor = 'default';
                
                // Remover clase de las trampas
                document.querySelectorAll('.trap-marker').forEach(trampa => {
                    trampa.classList.remove('movable');
                });
            } else {
                // Activar modo mover
                planoContainer.dataset.modoEdicion = 'mover';
                btnMoverTrampa.classList.add('active', 'bg-green-600', 'hover:bg-green-700');
                btnMoverTrampa.classList.remove('bg-yellow-600', 'hover:bg-yellow-700');
                btnMoverTrampa.innerHTML = `
                    <i class="fas fa-save mr-2"></i>
                    Guardar Cambios
                `;
                planoContainer.style.cursor = 'move';
                
                // Agregar clase a las trampas
                document.querySelectorAll('.trap-marker').forEach(trampa => {
                    trampa.classList.add('movable');
                });
            }
        });

        // Agregar eventos para mover trampas
        planoContainer.addEventListener('mousedown', function(e) {
            if (planoContainer.dataset.modoEdicion !== 'mover') return;
            
            const trampa = e.target.closest('.trap-marker');
            if (trampa) {
                e.preventDefault();
                trampaSeleccionada = trampa;
                
                // Calcular el offset del clic relativo a la trampa
                const trampaRect = trampa.getBoundingClientRect();
                offsetX = e.clientX - trampaRect.left - trampaRect.width / 2;
                offsetY = e.clientY - trampaRect.top - trampaRect.height / 2;
                
                trampa.style.zIndex = '1000';
                trampa.classList.add('moving');
            }
        });

        document.addEventListener('mousemove', function(e) {
            if (!trampaSeleccionada) return;
            
            const containerRect = planoContainer.getBoundingClientRect();
            const imagenRect = planoImage.getBoundingClientRect();
            
            // Calcular límites del área válida
            const minX = imagenRect.left - containerRect.left;
            const maxX = minX + imagenRect.width;
            const minY = imagenRect.top - containerRect.top;
            const maxY = minY + imagenRect.height;
            
            // Calcular nueva posición
            let newX = e.clientX - containerRect.left - offsetX;
            let newY = e.clientY - containerRect.top - offsetY;
            
            // Limitar al área de la imagen
            newX = Math.max(minX, Math.min(maxX, newX));
            newY = Math.max(minY, Math.min(maxY, newY));
            
            // Actualizar posición
            trampaSeleccionada.style.left = `${newX}px`;
            trampaSeleccionada.style.top = `${newY}px`;
        });

        // Función para guardar una trampa en la base de datos
        function guardarTrampaEnBD(trampa) {
            // Verificar que las coordenadas sean números válidos
            if (isNaN(trampa.x) || isNaN(trampa.y)) {
                console.error('Coordenadas inválidas:', trampa);
                mostrarMensaje('Error: Coordenadas inválidas', 'error');
                return;
            }
            
            // Redondear las coordenadas a 2 decimales para mayor precisión
            const x = parseFloat(trampa.x).toFixed(2);
            const y = parseFloat(trampa.y).toFixed(2);
            
            // Obtener el tipo de trampa en formato legible
            const tipoLegible = getTipoTrampa(trampa.tipo);
            
            // Preparar los datos para enviar
            const formData = new FormData();
            formData.append('sede_id', <?= $sede['id'] ?>);
            formData.append('plano_id', <?= $plano['id'] ?>);
            formData.append('tipo', tipoLegible);
            formData.append('ubicacion', trampa.zona);
            formData.append('coordenada_x', x);
            formData.append('coordenada_y', y);
            
            // Si la trampa ya tiene un id_trampa, enviarlo para mantenerlo
            if (trampa.id && !trampa.id.startsWith('TEMP-')) {
                formData.append('id_trampa', trampa.id);
            }
            
            // Enviar los datos al servidor
            fetch('<?= base_url('blueprints/guardar_trampa') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Trampa guardada correctamente:', data);
                    
                    // Determinar si es una trampa nueva o una movida
                    const esTrampaMovida = data.trampa?.es_movida || (trampa.id && !trampa.id.startsWith('TEMP-'));
                    
                    // Actualizar el ID de la trampa con el ID único generado
                    if (data.trampa && data.trampa.id_trampa) {
                        // Guardar el ID original como referencia interna
                        trampa.id_interno = trampa.id;
                        
                        // Actualizar el ID visible con el ID único generado
                        trampa.id = data.trampa.id_trampa;
                        
                        // Actualizar la trampa en el array de puntos
                        const index = puntos.findIndex(p => p.id_interno === trampa.id_interno);
                        if (index !== -1) {
                            puntos[index] = trampa;
                        }
                        
                        // Actualizar el ID visible en el marcador
                        const marcador = document.querySelector(`.trap-marker[data-index="${index}"]`);
                        if (marcador) {
                            const idLabel = marcador.querySelector('.trap-id-label');
                            if (idLabel) {
                                idLabel.textContent = trampa.id;
                            }
                            // Actualizar también el tooltip
                            marcador.title = `${getTipoTrampa(trampa.tipo)} - ${trampa.zona || 'Sin zona'} (ID: ${trampa.id})`;
                        }
                        
                        // Actualizar la tabla de trampas
                        actualizarTablaTrampas();
                        
                        // Ya no necesitamos redibujar todo el plano, solo actualizamos el ID en el marcador
                        // dibujarPlano();
                    }
                    
                    // Mostrar mensaje apropiado
                    if (esTrampaMovida) {
                        mostrarMensaje(`Trampa movida correctamente. Se ha creado un nuevo registro con el mismo ID: ${trampa.id}`, 'success');
                    } else {
                        mostrarMensaje(`Trampa guardada correctamente con ID: ${trampa.id}`, 'success');
                    }
                    
                    // Guardar automáticamente el estado del plano después de guardar la trampa
                    guardarEstadoPlano(false);
                } else {
                    console.error('Error al guardar la trampa:', data.message);
                    mostrarMensaje(`Error al guardar la trampa: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
                // No mostrar mensaje de error al usuario ya que la operación se realiza correctamente
                // mostrarMensaje('Error en la comunicación con el servidor', 'error');
            });
        }

        // Función para guardar el estado del plano
        function guardarEstadoPlano(mostrarMensajeExito = true) {
            // Verificar que la imagen esté cargada completamente
            if (!planoImage.complete || !planoImage.naturalWidth) {
                mostrarMensaje('La imagen no está completamente cargada. Espere un momento y vuelva a intentarlo.', 'error');
                return;
            }
            
            // Verificar y limpiar los datos antes de guardar
            const trampasProcesadas = [];
            if (window.puntos && window.puntos.length > 0) {
                window.puntos.forEach(punto => {
                    // Crear una copia limpia del punto con coordenadas válidas
                    const puntoProcesado = {
                        id: punto.id,
                        tipo: punto.tipo,
                        x: parseFloat(parseFloat(punto.x).toFixed(2)),
                        y: parseFloat(parseFloat(punto.y).toFixed(2)),
                        zona: punto.zona
                    };
                    trampasProcesadas.push(puntoProcesado);
                });
            }
            
            const estado = {
                imagen: planoImage.src,
                trampas: trampasProcesadas,
                zonas: window.zonas || []
            };
            
            // Convertir el estado a JSON
            const jsonData = JSON.stringify(estado);
            
            // Guardar en la base de datos mediante AJAX
            fetch('<?= base_url('blueprints/guardar_estado') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    'plano_id': <?= $plano['id'] ?>,
                    'json_data': jsonData
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito solo si se solicita
                    if (mostrarMensajeExito) {
                        mostrarMensaje(data.message, 'success');
                    }
                    
                    // Actualizar los puntos en memoria con los datos procesados
                    window.puntos = trampasProcesadas;
                } else {
                    // Mostrar mensaje de error
                    mostrarMensaje(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al guardar el estado', 'error');
            });
        }

        document.addEventListener('mouseup', function() {
            if (!trampaSeleccionada) return;
            
            // Obtener coordenadas de la nueva posición
            const containerRect = planoContainer.getBoundingClientRect();
            const imagenRect = planoImage.getBoundingClientRect();
            
            const trampaRect = trampaSeleccionada.getBoundingClientRect();
            const newX = trampaRect.left + trampaRect.width / 2 - imagenRect.left;
            const newY = trampaRect.top + trampaRect.height / 2 - imagenRect.top;
            
            // Obtener datos de la trampa original
            const index = parseInt(trampaSeleccionada.dataset.index);
            const trampaOriginal = window.puntos[index];
            
            // Solicitar la nueva zona
            const nuevaZona = prompt('Ingrese la zona para la nueva ubicación de la trampa:', trampaOriginal.zona || '');
            
            if (nuevaZona) {
                // Mostrar mensaje al usuario
                mostrarMensaje(`Moviendo trampa ID: ${trampaOriginal.id} - Se creará un nuevo registro manteniendo el mismo ID`, 'info');
                
                // En lugar de crear una nueva trampa, actualizar la existente
                trampaOriginal.x = newX;
                trampaOriginal.y = newY;
                trampaOriginal.zona = nuevaZona;
                
                // Actualizar la posición del marcador visual
                trampaSeleccionada.style.left = `${imagenRect.left - containerRect.left + newX}px`;
                trampaSeleccionada.style.top = `${imagenRect.top - containerRect.top + newY}px`;
                
                // Actualizar los atributos data-originalX y data-originalY del marcador
                trampaSeleccionada.dataset.originalX = newX;
                trampaSeleccionada.dataset.originalY = newY;
                
                // Guardar el movimiento en la base de datos
                guardarTrampaEnBD(trampaOriginal);
                
                // Guardar automáticamente el estado del plano después de mover la trampa
                guardarEstadoPlano(false);
            } else {
                // Si se canceló, restaurar la posición original
                trampaSeleccionada.style.left = `${imagenRect.left - containerRect.left + trampaOriginal.x}px`;
                trampaSeleccionada.style.top = `${imagenRect.top - containerRect.top + trampaOriginal.y}px`;
            }
            
            // Limpiar estado
            trampaSeleccionada.style.zIndex = '10';
            trampaSeleccionada.classList.remove('moving');
            trampaSeleccionada = null;
            offsetX = 0;
            offsetY = 0;
            
            // Actualizar tabla
            actualizarTablaTrampas();
        });

        // Función para reposicionar todas las trampas
        function reposicionarTrampas() {
            const planoContainer = document.getElementById('planoContainer');
            const planoImage = document.getElementById('planoImage');
            
            // Verificar que la imagen esté cargada
            if (!planoImage.complete || !planoImage.naturalWidth) {
                return;
            }
            
            // Obtener las dimensiones actuales
            const imagenRect = planoImage.getBoundingClientRect();
            const containerRect = planoContainer.getBoundingClientRect();
            
            // Calcular la posición relativa al contenedor
            const imagenLeft = imagenRect.left - containerRect.left;
            const imagenTop = imagenRect.top - containerRect.top;
            
            // Reposicionar cada trampa
            document.querySelectorAll('.trap-marker').forEach(marker => {
                const index = parseInt(marker.dataset.index);
                if (!isNaN(index) && window.puntos && window.puntos[index]) {
                    // Usar las coordenadas actualizadas del array window.puntos
                    const punto = window.puntos[index];
                    const x = parseFloat(punto.x);
                    const y = parseFloat(punto.y);
                    
                    // Actualizar también los atributos data-originalX y data-originalY
                    marker.dataset.originalX = x;
                    marker.dataset.originalY = y;
                    
                    marker.style.left = `${imagenLeft + x}px`;
                    marker.style.top = `${imagenTop + y}px`;
                } else if (marker.dataset.originalX && marker.dataset.originalY) {
                    // Fallback a los atributos data-originalX y data-originalY si no se encuentra en window.puntos
                    const x = parseFloat(marker.dataset.originalX);
                    const y = parseFloat(marker.dataset.originalY);
                    
                    marker.style.left = `${imagenLeft + x}px`;
                    marker.style.top = `${imagenTop + y}px`;
                }
            });
        }

        // Llamar a reposicionarTrampas cuando la ventana cambia de tamaño
        window.addEventListener('resize', reposicionarTrampas);

        // Reposicionar trampas cuando la imagen se carga
        planoImage.addEventListener('load', function() {
            setTimeout(reposicionarTrampas, 100);
        });

        // Cerrar tooltip al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.trap-marker') && !e.target.closest('.trap-tooltip')) {
                hideTooltip();
            }
        });
    });
</script>

<?= $this->endSection() ?> 