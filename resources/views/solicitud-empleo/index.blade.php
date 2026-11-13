<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Solicitud de Empleo - Grupo Lacour</title>

    {{-- Librería externa para generar PDF (por si la usas más adelante) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" defer></script>

    {{-- CSS extraído del index.php original --}}
    <link rel="stylesheet" href="{{ asset('css/solicitud-empleo.css') }}">

    {{-- JS propio (defer: se ejecuta cuando el DOM esté listo) --}}
    <script src="{{ asset('js/solicitud-empleo.js') }}" defer></script>
</head>

<body data-pdf-url="{{ asset('doc/descargar_formulario.pdf') }}">

    <div class="container" id="formContainer">
        <header>
            <div class="header-content">
               <img src="{{ asset('img/logoFirma.png') }}"
                 alt="Logo GRUPO LACOUR, C.A." class="header-logo">
                <div class="header-text">
                    <h2>SOLICITUD DE EMPLEO</h2>
                </div>
            </div>
        </header>

        <form id="solicitudForm" enctype="multipart/form-data">
            <!-- SECCIÓN 1: DATOS DE LA SOLICITUD -->
            <fieldset class="form-step active">
                <legend>Información del Cargo</legend>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Cargo que aspira:</label>
                        <input type="text" name="cargo_aspira" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha:</label>
                        <input type="date" name="fecha_solicitud" id="fecha_solicitud" readonly required>
                    </div>
                    <div class="form-group">
                        <label>¿Está empleado actualmente?</label>
                        <div class="radio-group">
                            <label><input type="radio" name="empleado_actual" value="Si"> Sí</label>
                            <label><input type="radio" name="empleado_actual" value="No"> No</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Sueldo que aspira:</label>
                        <input type="text" name="sueldo_aspira">
                    </div>
                </div>
            </fieldset>

            <!-- SECCIÓN 2: DATOS PERSONALES -->
            <fieldset class="form-step">
                <legend>Datos Personales</legend>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Apellidos y Nombres:</label>
                        <input type="text" name="nombres" required>
                    </div>
                    <div class="form-group">
                        <label>N° Cédula de Identidad:</label>
                        <div style="display:flex; gap:5px;">
                            <select name="cedula_tipo" style="width: 60px;">
                                <option value="V">V</option>
                                <option value="E">E</option>
                            </select>
                            <input type="text" name="cedula_numero" required>
                        </div>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Dirección:</label>
                        <input type="text" name="direccion">
                    </div>
                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="text" name="telefono">
                    </div>
                    <div class="form-group">
                        <label>Otra dirección donde localizarlo:</label>
                        <input type="text" name="otra_direccion">
                    </div>
                    <div class="form-group">
                        <label>Correo electrónico:</label>
                        <input type="email" name="correo">
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Sexo:</label>
                        <div class="radio-group">
                            <label><input type="radio" name="sexo" value="Masculino"> Masculino</label>
                            <label><input type="radio" name="sexo" value="Femenino"> Femenino</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Estado civil:</label>
                        <select name="estado_civil">
                            <option value="">Seleccione...</option>
                            <option value="Soltero">Soltero (A)</option>
                            <option value="Divorciado">Divorciado (A)</option>
                            <option value="Casado">Casado (A)</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Lugar de nacimiento:</label>
                        <input type="text" name="lugar_nacimiento">
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Fecha de Nac.:</label>
                        <input type="date" name="fecha_nacimiento">
                    </div>
                    <div class="form-group">
                        <label>Edad:</label>
                        <input type="number" name="edad">
                    </div>
                    <div class="form-group">
                        <label>Nacionalidad:</label>
                        <select name="nacionalidad">
                            <option value="Venezolana">Venezolana</option>
                            <option value="Extranjero">Extranjero</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Si es Venezolano Naturalizado <span class="tooltip-container">?<span class="tooltip-text">Indique los datos de la Gaceta Oficial donde aparece su naturalización.</span></span></label>
                        <div style="display:flex; gap:5px;">
                            <input type="text" name="gaceta_num" placeholder="Gaceta Nº">
                            <input type="date" name="gaceta_fecha">
                        </div>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Tiene vehículo:</label>
                        <div class="radio-group">
                            <label><input type="radio" name="vehiculo" value="Si"> Sí</label>
                            <label><input type="radio" name="vehiculo" value="No"> No</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Posee licencia de conducir:</label>
                        <div class="radio-group">
                            <label><input type="radio" name="licencia" value="Si"> Sí</label>
                            <label><input type="radio" name="licencia" value="No"> No</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Grado / Número:</label>
                        <input type="text" name="licencia_detalles">
                    </div>
                    <div class="form-group">
                        <label>Certificado Médico N° / Fecha Vcto:</label>
                        <div style="display:flex; gap:5px;">
                            <input type="text" name="certificado_medico">
                            <input type="date" name="fecha_vcto_medico">
                        </div>
                    </div>
                </div>
                
                <h4 style="margin-top: 15px; font-size: 14px; color: var(--primary-color);">Tallas y Medidas</h4>
                <div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));">
                    <div class="form-group">
                        <label>Camisa o Blusa:</label>
                        <input type="text" name="talla_camisa">
                    </div>
                    <div class="form-group">
                        <label>Pantalón:</label>
                        <input type="text" name="talla_pantalon">
                    </div>
                    <div class="form-group">
                        <label>Botas:</label>
                        <input type="text" name="talla_botas">
                    </div>
                    <div class="form-group">
                        <label>Estatura:</label>
                        <input type="text" name="estatura">
                    </div>
                    <div class="form-group">
                        <label>Peso (Kg):</label>
                        <input type="text" name="peso">
                    </div>
                </div>
            </fieldset>

            <!-- SECCIÓN 3: DATOS FAMILIARES -->
            <fieldset class="form-step">
                <legend>Datos Familiares</legend>
                <p style="font-size: 12px; color: #666; margin-bottom: 10px;">Agregue solo los familiares directos (Padres, Cónyuge, Hijos) que desee registrar.</p>
                <div class="table-responsive">
                    <table id="family-table">
                        <thead>
                            <tr>
                                <th>Parentesco</th>
                                <th>Apellidos y Nombres</th>
                                <th>Teléfono</th>
                                <th>Cédula de Identidad</th>
                                <th>Fecha de Nac.</th>
                                <th>Edad</th>
                                <th class="no-print">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="family-tbody"></tbody>
                    </table>
                </div>
                <button type="button" id="add-family-btn" class="btn-add-row">➕ Agregar Familiar</button>
            </fieldset>

            <!-- SECCIÓN 4: EDUCACIÓN -->
            <fieldset class="form-step">
                <legend>Educación</legend>
                <p style="font-size: 12px; color: #666; margin-bottom: 10px;">Registre sus estudios comenzando por el más reciente.</p>
                <div class="table-responsive">
                    <table id="education-table">
                        <thead>
                            <tr>
                                <th>Instrucción</th>
                                <th>Instituto</th>
                                <th>Años Cursados</th>
                                <th>Fecha Desde</th>
                                <th>Fecha Hasta</th>
                                <th>Obtuvo Título</th>
                                <th>Especialidad</th>
                                <th class="no-print">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="education-tbody"></tbody>
                    </table>
                </div>
                <button type="button" id="add-education-btn" class="btn-add-row">➕ Agregar Educación</button>
            </fieldset>

            <!-- SECCIÓN 5: EXPERIENCIA LABORAL -->
            <fieldset class="form-step">
                <legend>Experiencia Laboral</legend>
                <div class="form-grid" style="margin-bottom: 20px;">
                    <div class="form-group">
                        <label>¿Ha trabajado anteriormente en esta empresa?</label>
                        <div class="radio-group">
                            <label><input type="radio" name="trabajo_previo" value="Si"> Sí</label>
                            <label><input type="radio" name="trabajo_previo" value="No"> No</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Cargo que desempeñado:</label>
                        <input type="text" name="trabajo_previo_cual">
                    </div>
                    <div class="form-group">
                        <label>¿Tiene algún familiar en las empresa?</label>
                        <div class="radio-group">
                            <label><input type="radio" name="familiar_empresa" value="Si"> Sí</label>
                            <label><input type="radio" name="familiar_empresa" value="No"> No</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nombre del familiar:</label>
                        <input type="text" name="familiar_empresa_nombre">
                    </div>
                </div>
                <div id="experience-container"></div>
                <button type="button" id="add-experience-btn" class="btn-add-row" style="margin-top: 10px;">➕ Agregar Experiencia Laboral</button>
            </fieldset>

            <!-- SECCIÓN 6: REFERENCIAS PERSONALES -->
            <fieldset class="form-step">
                <legend>Referencias Personales</legend>
                <p style="font-size: 12px; color: #666; margin-bottom: 10px;">Indique al menos 3 personas que no sean familiares.</p>
                <div class="table-responsive">
                    <table id="reference-table">
                        <thead>
                            <tr>
                                <th>Apellidos y Nombres</th>
                                <th>Empresa</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Ocupación</th>
                                <th>Tiempo que lo conoce</th>
                                <th class="no-print">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="reference-tbody"></tbody>
                    </table>
                </div>
                <button type="button" id="add-reference-btn" class="btn-add-row">➕ Agregar Referencia</button>
            </fieldset>

            <!-- SECCIÓN 7: DOCUMENTOS ANEXOS -->
            <fieldset class="form-step">
                <legend>Documentos Anexos</legend>
                <p style="font-size: 13px; color: #555; margin-bottom: 15px;">
                    Por favor, adjunte los siguientes documentos requeridos. Marque la casilla para confirmar que está enviando el archivo por medio de la página web.
                </p>
                <div class="doc-upload-container">
                    <div class="doc-upload-item">
                        <label>
                            <input type="checkbox" name="doc_cv_check" id="check_cv"> Currículum Vitae
                        </label>
                        <div style="display: flex; align-items: center;">
                            <div class="file-input-wrapper">
                                <button type="button" class="file-btn">📁 Seleccionar Archivo</button>
                                <input type="file" name="file_cv" id="file_cv" accept=".pdf,.doc,.docx" onchange="updateFileName(this, 'name_cv')">
                            </div>
                            <span id="name_cv" class="file-name-display"></span>
                        </div>
                    </div>
                    <div class="doc-upload-item">
                        <label>
                            <input type="checkbox" name="doc_cedula_check" id="check_cedula"> Cédula de Identidad
                        </label>
                        <div style="display: flex; align-items: center;">
                            <div class="file-input-wrapper">
                                <button type="button" class="file-btn">📁 Seleccionar Archivo</button>
                                <input type="file" name="file_cedula" id="file_cedula" accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName(this, 'name_cedula')">
                            </div>
                            <span id="name_cedula" class="file-name-display"></span>
                        </div>
                    </div>
                    <div class="doc-upload-item">
                        <label>
                            <input type="checkbox" name="doc_rif_check" id="check_rif"> RIF
                        </label>
                        <div style="display: flex; align-items: center;">
                            <div class="file-input-wrapper">
                                <button type="button" class="file-btn">📁 Seleccionar Archivo</button>
                                <input type="file" name="file_rif" id="file_rif" accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName(this, 'name_rif')">
                            </div>
                            <span id="name_rif" class="file-name-display"></span>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- SECCIÓN 8: DECLARACIÓN -->
            <fieldset class="form-step">
                <legend>Declaración</legend>
                <p style="font-size: 13px; text-align: justify; line-height: 1.5;">
                    Declaro que la información y datos suministrados por mí en este formulario son verídicos y correctos, por lo que autorizo a <strong>GRUPO LACOUR, C.A.</strong> o a quien este asigne a efectuar cualquier averiguación que se estime necesaria.
                    <br><br>
                    Estoy conforme en someterme a un examen médico practicado por cuenta de la empresa, también me comprometo en caso de ser aceptado, atenerme a las políticas, normas y procedimientos establecidos por la misma.
                </p>
                <div class="terms-group">
                    <input type="checkbox" id="acceptTerms" name="acceptTerms" required>
                    <label for="acceptTerms">
                        He leído, comprendido y acepto los 
                        <span class="terms-link" id="openTerms">Términos y Condiciones</span> 
                        y la política de tratamiento de datos personales de GRUPO LACOUR, C.A. Acepto que esta acción constituye mi firma electrónica.
                    </label>
                </div>
            </fieldset>

            <!-- NAVEGACIÓN PARA MÓVILES (PASO A PASO) -->
            <div class="step-nav no-print">
                <button type="button" id="prevBtn" onclick="changeStep(-1)" disabled>❮ Anterior</button>
                <span class="step-indicator" id="stepIndicator">Paso 1 de 8</span>
                <button type="button" id="nextBtn" onclick="changeStep(1)">Siguiente ❯</button>
            </div>

            <!-- BOTONES DE ACCIÓN FINALES -->
            <div class="action-buttons no-print" id="actionButtons">
                <button type="submit" class="submit-btn">Enviar Solicitud</button>
                <button type="button" id="downloadBtn" class="download-btn">📄 Descargar Formulario</button>
            </div>
        </form>
    </div>

    <!-- MODAL DE TÉRMINOS Y CONDICIONES -->
    <div class="help-modal" id="termsModal">
        <div class="help-content">
            <span class="close-help" id="closeTerms">&times;</span>
            <h2>Términos, Condiciones y Política de Datos</h2>
            <div style="font-size: 13px; line-height: 1.6; text-align: justify; color: #444;">
                <p><strong>1. Veracidad de la Información:</strong> El postulante declara bajo fe de juramento que todos los datos suministrados en este formulario son verídicos, completos y actualizados. Cualquier falsedad u omisión podrá ser causa de anulación de la solicitud o despido en caso de haber sido contratado.</p>
                <p><strong>2. Autorización de Verificación:</strong> El postulante autoriza expresamente a GRUPO LACOUR, C.A. a realizar todas las averiguaciones que considere pertinentes, incluyendo referencias laborales, personales, antecedentes penales y verificaciones académicas.</p>
                <p><strong>3. Examen Médico:</strong> El postulante se compromete a someterse a los exámenes médicos y psicotécnicos que la empresa requiera como parte del proceso de selección, los cuales serán costeados por la empresa.</p>
                <p><strong>4. Tratamiento de Datos Personales:</strong> Los datos aquí suministrados serán tratados de forma confidencial y utilizados exclusivamente para fines de reclutamiento y selección de personal, de acuerdo con las leyes vigentes de protección de datos.</p>
                <p><strong>5. Firma Electrónica:</strong> Al marcar la casilla de aceptación, el postulante otorga su consentimiento expreso y equipara esta acción a una firma física manuscrita, reconociendo la validez legal de este formulario digital.</p>
            </div>
            <button type="button" class="btn-add-row" id="acceptTermsBtn" style="width: 100%; justify-content: center; margin-top: 20px;">Entendido, Aceptar Términos</button>
        </div>
    </div>


</body>
</html>