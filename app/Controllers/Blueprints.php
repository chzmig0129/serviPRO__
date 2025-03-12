<?php

namespace App\Controllers;
use App\Models\SedeModel;
use App\Models\PlanoModel;
use CodeIgniter\I18n\Time;

class Blueprints extends BaseController
{
    public function index()
    {
        // Cargar el modelo de sedes
        $sedeModel = new SedeModel();
        
        // Obtener todas las sedes
        $data['sedes'] = $sedeModel->findAll();
        
        // Cargar la vista con los datos
        return view('blueprints/index', $data);
    }

    public function view($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Sede no especificada');
        }

        // Cargar modelos
        $sedeModel = new SedeModel();
        $planoModel = new PlanoModel();

        // Obtener información de la sede
        $sede = $sedeModel->find($id);
        if (!$sede) {
            return redirect()->to('/blueprints')->with('error', 'Sede no encontrada');
        }

        // Obtener planos de la sede
        $planos = $planoModel->where('sede_id', $id)->findAll();

        // Procesar las previsualizaciones de los planos
        foreach ($planos as &$plano) {
            $plano['preview_image'] = $this->getPreviewImage($plano);
        }

        $data = [
            'sede' => $sede,
            'planos' => $planos
        ];

        return view('blueprints/view', $data);
    }

    /**
     * Obtiene una imagen de previsualización del archivo JSON del plano
     * 
     * @param array $plano Datos del plano
     * @return string|null URL de la imagen de previsualización o null si no hay imagen
     */
    private function getPreviewImage($plano)
    {
        if (empty($plano['archivo'])) {
            return null;
        }

        try {
            $archivoData = json_decode($plano['archivo'], true);
            if (isset($archivoData['imagen']) && !empty($archivoData['imagen'])) {
                return $archivoData['imagen'];
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al procesar la imagen del plano: ' . $e->getMessage());
        }

        return null;
    }

    public function guardar_plano()
    {
        // Validar los datos del formulario
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre' => 'required|max_length[255]',
            'descripcion' => 'required',
            'sede_id' => 'required|numeric'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Por favor, complete todos los campos correctamente.');
        }

        try {
            // Obtener los datos del formulario
            $data = [
                'nombre' => $this->request->getPost('nombre'),
                'descripcion' => $this->request->getPost('descripcion'),
                'sede_id' => $this->request->getPost('sede_id'),
                'fecha_creacion' => Time::now('America/Mexico_City')->format('Y-m-d H:i:s')
            ];

            // Guardar los datos en la base de datos
            $planoModel = new PlanoModel();
            $planoId = $planoModel->insert($data, true); // El segundo parámetro true hace que retorne el ID insertado

            // Redirigir a la vista del plano con mensaje de éxito
            return redirect()->to('blueprints/viewplano/' . $planoId)
                            ->with('message', 'Plano guardado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error al guardar el plano. Por favor, intente nuevamente.');
        }
    }

    // Agregar el método para ver un plano específico
    public function viewplano($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        // Cargar modelos
        $planoModel = new PlanoModel();
        $sedeModel = new SedeModel();

        // Obtener información del plano
        $plano = $planoModel->find($id);
        if (!$plano) {
            return redirect()->to('/blueprints')->with('error', 'Plano no encontrado');
        }

        // Obtener información de la sede asociada
        $sede = $sedeModel->find($plano['sede_id']);

        $data = [
            'plano' => $plano,
            'sede' => $sede
        ];

        return view('blueprints/viewplano', $data);
    }

    // Método para guardar el estado del plano (JSON)
    public function guardar_estado()
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }

        // Obtener los datos JSON y el ID del plano
        $planoId = $this->request->getPost('plano_id');
        $jsonData = $this->request->getPost('json_data');

        if (!$planoId || !$jsonData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
        }

        try {
            // Cargar el modelo de planos
            $planoModel = new PlanoModel();
            
            // Verificar que el plano existe
            $plano = $planoModel->find($planoId);
            if (!$plano) {
                return $this->response->setJSON(['success' => false, 'message' => 'Plano no encontrado']);
            }
            
            // Decodificar los datos JSON
            $estadoData = json_decode($jsonData, true);
            
            // Verificar si hay una imagen en los datos
            if (isset($estadoData['imagen']) && !empty($estadoData['imagen'])) {
                // Verificar si es una imagen base64
                if (strpos($estadoData['imagen'], 'data:image') === 0 && strpos($estadoData['imagen'], 'base64,') !== false) {
                    // Extraer el tipo de imagen y los datos base64
                    $partes = explode('base64,', $estadoData['imagen']);
                    if (count($partes) === 2) {
                        $cabecera = $partes[0];
                        $datos = $partes[1];
                        
                        // Determinar la extensión del archivo basado en el tipo MIME
                        $extension = 'png'; // Por defecto
                        if (strpos($cabecera, 'image/jpeg') !== false) {
                            $extension = 'jpg';
                        } elseif (strpos($cabecera, 'image/gif') !== false) {
                            $extension = 'gif';
                        }
                        
                        // Generar un nombre de archivo único
                        $nombreArchivo = 'plano_' . $planoId . '_' . time() . '.' . $extension;
                        $rutaArchivo = FCPATH . 'uploads/planos/' . $nombreArchivo;
                        
                        // Guardar la imagen en el sistema de archivos
                        if (file_put_contents($rutaArchivo, base64_decode($datos))) {
                            // Actualizar el JSON para que contenga la ruta de la imagen en lugar de los datos base64
                            $rutaRelativa = base_url('uploads/planos/' . $nombreArchivo);
                            $estadoData['imagen'] = $rutaRelativa;
                            
                            // Actualizar el JSON
                            $jsonData = json_encode($estadoData);
                        } else {
                            return $this->response->setJSON(['success' => false, 'message' => 'Error al guardar la imagen en el servidor']);
                        }
                    }
                } elseif (strpos($estadoData['imagen'], base_url('uploads/planos/')) === 0) {
                    // La imagen ya es una URL, no necesitamos hacer nada
                }
            }
            
            // Actualizar el campo 'archivo' con los datos JSON
            $planoModel->update($planoId, ['archivo' => $jsonData]);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Estado del plano guardado correctamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error al guardar el estado: ' . $e->getMessage()]);
        }
    }
    
    // Método para obtener el estado actual del plano
    public function obtener_estado($id = null)
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }
        
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID de plano no especificado']);
        }
        
        try {
            // Cargar el modelo de planos
            $planoModel = new PlanoModel();
            
            // Obtener el plano
            $plano = $planoModel->find($id);
            if (!$plano) {
                return $this->response->setJSON(['success' => false, 'message' => 'Plano no encontrado']);
            }
            
            // Devolver el plano con su estado
            return $this->response->setJSON([
                'success' => true, 
                'plano' => $plano
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error al obtener el estado: ' . $e->getMessage()]);
        }
    }
    
    // Método para guardar una trampa en la base de datos
    public function guardar_trampa()
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }
        
        // Obtener los datos de la trampa
        $sedeId = $this->request->getPost('sede_id');
        $planoId = $this->request->getPost('plano_id');
        $tipo = $this->request->getPost('tipo');
        $ubicacion = $this->request->getPost('ubicacion');
        $coordenadaX = $this->request->getPost('coordenada_x');
        $coordenadaY = $this->request->getPost('coordenada_y');
        $idTrampa = $this->request->getPost('id_trampa'); // Obtener id_trampa si existe
        
        if (!$sedeId || !$planoId || !$tipo || !$ubicacion || !$coordenadaX || !$coordenadaY) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
        }
        
        try {
            // Cargar el modelo de trampas
            $trampaModel = new \App\Models\TrampaModel();
            
            // Preparar los datos para guardar
            $data = [
                'sede_id' => $sedeId,
                'plano_id' => $planoId,
                'tipo' => $tipo,
                'ubicacion' => $ubicacion,
                'coordenada_x' => $coordenadaX,
                'coordenada_y' => $coordenadaY,
                'fecha_instalacion' => date('Y-m-d H:i:s')
            ];
            
            // Si se proporcionó un id_trampa, usarlo en lugar de generar uno nuevo
            if ($idTrampa) {
                $data['id_trampa'] = $idTrampa;
                $esTrampaMovida = true;
            } else {
                $esTrampaMovida = false;
            }
            
            // Guardar la trampa y obtener el ID insertado
            $trampaId = $trampaModel->insert($data);
            
            // Obtener el registro completo para recuperar el id_trampa generado
            $trampa = $trampaModel->find($trampaId);
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => $esTrampaMovida ? 'Trampa movida correctamente' : 'Trampa guardada correctamente',
                'trampa' => [
                    'id' => $trampaId,
                    'id_trampa' => $trampa['id_trampa'] ?? '',
                    'es_movida' => $esTrampaMovida
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error al guardar la trampa: ' . $e->getMessage()
            ]);
        }
    }

    // Método para guardar una incidencia en la base de datos
    public function guardar_incidencia()
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }
        
        // Obtener los datos de la incidencia
        $trampaId = $this->request->getPost('trampa_id');
        $tipoPlaga = $this->request->getPost('tipo_plaga');
        $cantidadOrganismos = $this->request->getPost('cantidad_organismos');
        $tipoIncidencia = $this->request->getPost('tipo_incidencia');
        $notas = $this->request->getPost('notas');
        $inspector = $this->request->getPost('inspector');
        
        // Registrar los datos recibidos para depuración
        log_message('info', 'Datos de incidencia recibidos: ' . json_encode([
            'trampa_id' => $trampaId,
            'tipo_plaga' => $tipoPlaga,
            'cantidad_organismos' => $cantidadOrganismos,
            'tipo_incidencia' => $tipoIncidencia,
            'notas' => $notas,
            'inspector' => $inspector
        ]));
        
        if (!$trampaId || !$tipoPlaga) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
        }
        
        // Verificar que se haya proporcionado una fecha
        $fechaIncidencia = $this->request->getPost('fecha_incidencia');
        if (!$fechaIncidencia) {
            return $this->response->setJSON(['success' => false, 'message' => 'Debe proporcionar una fecha para la incidencia']);
        }
        
        try {
            // Cargar el modelo de trampas
            $trampaModel = new \App\Models\TrampaModel();
            
            // Intentar buscar la trampa por id_trampa primero
            $trampa = $trampaModel->where('id_trampa', $trampaId)->orderBy('id', 'DESC')->first();
            log_message('info', 'Búsqueda por id_trampa: ' . ($trampa ? 'Encontrada' : 'No encontrada'));
            
            // Si no se encuentra por id_trampa, intentar buscar por id
            if (!$trampa) {
                $trampa = $trampaModel->find($trampaId);
                log_message('info', 'Búsqueda por id: ' . ($trampa ? 'Encontrada' : 'No encontrada'));
            }
            
            // Si aún no se encuentra, intentar buscar por id numérico
            if (!$trampa && is_numeric($trampaId)) {
                $trampa = $trampaModel->find((int)$trampaId);
                log_message('info', 'Búsqueda por id numérico: ' . ($trampa ? 'Encontrada' : 'No encontrada'));
            }
            
            if (!$trampa) {
                log_message('error', 'No se encontró la trampa con ID: ' . $trampaId);
                return $this->response->setJSON(['success' => false, 'message' => 'No se encontró la trampa especificada (ID: ' . $trampaId . ')']);
            }
            
            // Usar el ID de la trampa encontrada
            $idTrampaReciente = $trampa['id'];
            // Obtener el ID de la sede asociada a la trampa
            $idSede = $trampa['sede_id'];
            log_message('info', 'ID de trampa encontrado: ' . $idTrampaReciente . ', ID de sede: ' . $idSede);
            
            // Cargar el modelo de incidencias
            $incidenciaModel = new \App\Models\IncidenciaModel();
            
            // Formatear la fecha de incidencia para MySQL (YYYY-MM-DD HH:MM:SS)
            $fechaFormateada = date('Y-m-d H:i:s', strtotime($fechaIncidencia));
            
            // Preparar los datos para guardar
            $data = [
                'id_trampa' => $idTrampaReciente, // Usamos el ID de la trampa encontrada
                'sede_id' => $idSede, // Agregamos el ID de la sede
                'fecha' => $fechaFormateada, // Usamos la fecha proporcionada por el usuario
                'tipo_plaga' => $tipoPlaga,
                'cantidad_organismos' => $cantidadOrganismos ?? 0,
                'tipo_incidencia' => $tipoIncidencia ?? 'Captura',
                'notas' => $notas,
                'inspector' => $inspector ?? 'Sistema'
            ];
            
            // Guardar la incidencia
            $incidenciaId = $incidenciaModel->insert($data);
            log_message('info', 'Incidencia guardada con ID: ' . $incidenciaId);
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Incidencia registrada correctamente',
                'incidencia_id' => $incidenciaId
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al guardar incidencia: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error al guardar la incidencia: ' . $e->getMessage()
            ]);
        }
    }
} 