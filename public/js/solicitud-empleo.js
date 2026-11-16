        // --- 1. Fecha Automática ---
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('fecha_solicitud').value = today;

        // --- 2. Lógica del Stepper (Paso a paso Móvil) ---
        let currentStep = 0;
        const steps = document.querySelectorAll('.form-step');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const stepIndicator = document.getElementById('stepIndicator');
        const actionButtons = document.getElementById('actionButtons');

        function updateStepVisibility() {
            if (window.innerWidth > 768) {
                steps.forEach(step => step.style.display = 'block');
                document.querySelector('.step-nav').style.display = 'none';
                actionButtons.style.display = 'flex';
                return;
            }

            document.querySelector('.step-nav').style.display = 'flex';
            
            steps.forEach((step, index) => {
                step.style.display = index === currentStep ? 'block' : 'none';
            });

            stepIndicator.textContent = `Paso ${currentStep + 1} de ${steps.length}`;
            prevBtn.disabled = currentStep === 0;

            if (currentStep === steps.length - 1) {
                nextBtn.style.display = 'none';
                actionButtons.style.display = 'flex';
            } else {
                nextBtn.style.display = 'block';
                actionButtons.style.display = 'none';
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function changeStep(direction) {
            currentStep += direction;
            if (currentStep < 0) currentStep = 0;
            if (currentStep >= steps.length) currentStep = steps.length - 1;
            updateStepVisibility();
        }

        window.addEventListener('resize', updateStepVisibility);
        updateStepVisibility();

        // --- Calcular Edad Automáticamente ---
        function calcularEdad() {
            const inputNacimiento = document.querySelector('input[name="fecha_nacimiento"]');
            const inputEdad = document.querySelector('input[name="edad"]');
            if (!inputNacimiento || !inputEdad) return;

            const fechaNacimiento = inputNacimiento.value;
            if (!fechaNacimiento) {
                inputEdad.value = '';
                return;
            }

            const nacimiento = new Date(fechaNacimiento + 'T00:00:00');
            const hoy = new Date();
            let edad = hoy.getFullYear() - nacimiento.getFullYear();
            const mes = hoy.getMonth() - nacimiento.getMonth();
            if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                edad--;
            }
            inputEdad.value = (edad > 0 ? edad : '').toString();
        }

        document.querySelector('input[name="fecha_nacimiento"]').addEventListener('change', calcularEdad);
        window.addEventListener('load', calcularEdad);

        // --- 3. Función para mostrar el nombre del archivo ---
        function updateFileName(input, spanId) {
            const span = document.getElementById(spanId);
            if (input.files && input.files.length > 0) {
                span.textContent = "✅ " + input.files[0].name;
            } else {
                span.textContent = "";
            }
        }

        // --- Modal de Términos y Condiciones ---
        const termsModal = document.getElementById('termsModal');
        const openTerms = document.getElementById('openTerms');
        const closeTerms = document.getElementById('closeTerms');
        const acceptTermsBtn = document.getElementById('acceptTermsBtn');
        const acceptTermsCheckbox = document.getElementById('acceptTerms');

        openTerms.addEventListener('click', (e) => {
            e.preventDefault();
            termsModal.style.display = 'flex';
        });

        closeTerms.addEventListener('click', () => termsModal.style.display = 'none');
        
        acceptTermsBtn.addEventListener('click', () => {
            acceptTermsCheckbox.checked = true;
            termsModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === termsModal) termsModal.style.display = 'none';
        });

        // --- 4. Lógica para Datos Dinámicos ---
        
        // Familiares
        const familyTbody = document.getElementById('family-tbody');
        document.getElementById('add-family-btn').addEventListener('click', () => addFamilyRow());

        function addFamilyRow(defaultRelation = '') {
            const tr = document.createElement('tr');
            const options = ['Padre', 'Madre', 'Cónyuge', 'Hijo/a', 'Hermano/a', 'Otro'].map(rel => 
                `<option value="${rel}" ${defaultRelation === rel ? 'selected' : ''}>${rel}</option>`
            ).join('');

            tr.innerHTML = `
                <td data-label="Parentesco">
                    <select name="familiar_parentesco[]" required>
                        <option value="">Seleccione...</option>
                        ${options}
                    </select>
                </td>
                <td data-label="Nombres"><input type="text" name="familiar_nombre[]" required></td>
                <td data-label="Teléfono"><input type="text" name="familiar_telefono[]"></td>
                <td data-label="Cédula"><input type="text" name="familiar_cedula[]"></td>
                <td data-label="F. de Nac."><input type="date" name="familiar_fecha_nac[]"></td>
                <td data-label="Edad"><input type="number" name="familiar_edad[]"></td>
                <td style="text-align: center;" class="no-print" data-label="Acción">
                    <button type="button" class="btn-remove-row" onclick="this.closest('tr').remove()">❌</button>
                </td>
            `;
            familyTbody.appendChild(tr);
        }
        addFamilyRow('Padre');
        addFamilyRow('Madre');

        // Educación
        const educationTbody = document.getElementById('education-tbody');
        document.getElementById('add-education-btn').addEventListener('click', () => addEducationRow());

        function addEducationRow() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td data-label="Instrucción">
                    <select name="edu_nivel[]" required>
                        <option value="">Seleccione...</option>
                        <option value="Secundaria">Secundaria</option>
                        <option value="Técnica">Técnica</option>
                        <option value="Universitaria">Universitaria</option>
                        <option value="Post-grado">Post-grado</option>
                        <option value="Maestría">Maestría</option>
                        <option value="Otro">Otro</option>
                    </select>
                </td>
                <td data-label="Instituto"><input type="text" name="edu_instituto[]"></td>
                <td data-label="Años"><input type="number" name="edu_anos[]"></td>
                <td data-label="Desde"><input type="date" name="edu_desde[]"></td>
                <td data-label="Hasta"><input type="date" name="edu_hasta[]"></td>
                <td data-label="Título">
                    <select name="edu_titulo[]">
                        <option value="Si">Sí</option>
                        <option value="No">No</option>
                        <option value="En estudio">En estudio</option>
                    </select>
                </td>
                <td data-label="Especialidad"><input type="text" name="edu_especialidad[]"></td>
                <td style="text-align: center;" class="no-print" data-label="Acción">
                    <button type="button" class="btn-remove-row" onclick="this.closest('tr').remove()">❌</button>
                </td>
            `;
            educationTbody.appendChild(tr);
        }
        addEducationRow();

        // Experiencia
        const experienceContainer = document.getElementById('experience-container');
        let expCount = 0;
        document.getElementById('add-experience-btn').addEventListener('click', () => addExperienceBlock());

        function addExperienceBlock() {
            expCount++;
            const div = document.createElement('div');
            div.className = 'experiencia-block';
            
            div.innerHTML = `
                <h4>Empleo ${expCount}</h4>
                <button type="button" class="btn-remove-block no-print" onclick="this.parentElement.remove()">❌ Eliminar</button>
                
                <div class="form-grid">
                    <div class="form-group"><label>Empresa:</label><input type="text" name="exp_empresa[]"></div>
                    <div class="form-group"><label>Dirección:</label><input type="text" name="exp_direccion[]"></div>
                    <div class="form-group"><label>Teléfono:</label><input type="text" name="exp_telefono[]"></div>
                    <div class="form-group"><label>Jefe inmediato:</label><input type="text" name="exp_jefe[]"></div>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label>Fecha ingreso:</label><input type="date" name="exp_fecha_ingreso[]"></div>
                    <div class="form-group"><label>Fecha egreso:</label><input type="date" name="exp_fecha_egreso[]"></div>
                    <div class="form-group"><label>Sueldo inicial:</label><input type="text" name="exp_sueldo_inicial[]"></div>
                    <div class="form-group"><label>Sueldo final:</label><input type="text" name="exp_sueldo_final[]"></div>
                    <div class="form-group"><label>Cargo desempeñado:</label><input type="text" name="exp_cargo[]"></div>
                    <div class="form-group"><label>Motivo del retiro:</label><input type="text" name="exp_motivo[]"></div>
                </div>
                <div class="form-group">
                    <label>Describa brevemente las labores realizadas:</label>
                    <textarea name="exp_labores[]" rows="2"></textarea>
                </div>
            `;
            experienceContainer.appendChild(div);
        }
        addExperienceBlock();

        // Referencias
        const referenceTbody = document.getElementById('reference-tbody');
        document.getElementById('add-reference-btn').addEventListener('click', () => addReferenceRow());

        function addReferenceRow() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td data-label="Nombres"><input type="text" name="ref_nombre[]" required></td>
                <td data-label="Empresa"><input type="text" name="ref_empresa[]"></td>
                <td data-label="Dirección"><input type="text" name="ref_direccion[]"></td>
                <td data-label="Teléfono"><input type="text" name="ref_telefono[]"></td>
                <td data-label="Ocupación"><input type="text" name="ref_ocupacion[]"></td>
                <td data-label="Tiempo"><input type="text" name="ref_tiempo[]"></td>
                <td style="text-align: center;" class="no-print" data-label="Acción">
                    <button type="button" class="btn-remove-row" onclick="this.closest('tr').remove()">❌</button>
                </td>
            `;
            referenceTbody.appendChild(tr);
        }
        addReferenceRow();
        addReferenceRow();
        addReferenceRow();

        // --- 5. Lógica para Descargar PDF ---
        // CORRECCIÓN [Nivel Crítico]:
        // Error: Apuntaba a 'public/doc/descargar_formulario.pdf'. En Laravel, 'public/' es el DocumentRoot,
        //        por lo que causaba un error 404 (Not Found) al descargar.
        // Solución: Usar la URL provista en el atributo 'data-pdf-url' del body o la ruta raíz '/doc/descargar_formulario.pdf'.
        document.getElementById('downloadBtn').addEventListener('click', () => {
            const pdfUrl = document.body.dataset.pdfUrl || '/doc/descargar_formulario.pdf';
            window.location.href = pdfUrl;
        });

        // --- 6. Validación y Envío del Formulario ---
        const form = document.getElementById('solicitudForm');
        
        form.addEventListener('submit', (e) => {
            e.preventDefault(); 
            
            if (!acceptTermsCheckbox.checked) {
                alert('Para enviar su solicitud, debe leer y aceptar los Términos y Condiciones.');
                termsModal.style.display = 'flex'; 
                return;
            }

            if (document.getElementById('check_cv').checked && !document.getElementById('file_cv').files.length) {
                alert('Ha marcado el Currículum Vitae, pero no ha seleccionado un archivo.');
                return;
            }

            // CORRECCIÓN [Nivel Crítico]:
            // Error 1: fetch llamaba a 'backend/guardar_solicitud.php' (archivo inexistente de PHP nativo -> Error 404).
            // Solución: Leer dinámicamente el endpoint del atributo data-url del formulario (route('solicitud.store')).
            const targetUrl = form.getAttribute('data-url') || form.action || '/solicitud-empleo';

            // CORRECCIÓN [Nivel Crítico]:
            // Error 2: Faltaba el header 'Accept: application/json'. Sin este header, ante un error de validación (HTTP 422),
            //          Laravel responde con una redirección HTTP 302 hacia '/', el fetch sigue la redirección y devuelve HTML,
            //          lo que provocaba una excepción fatal: SyntaxError: Unexpected token '<' al hacer response.json().
            // Solución: Añadir 'Accept': 'application/json' y el header de seguridad 'X-CSRF-TOKEN'.
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

            const formData = new FormData(form);

            // Deshabilitar botón durante el envío para evitar envíos dobles accidentales
            const submitBtn = form.querySelector('.submit-btn');
            const originalBtnText = submitBtn ? submitBtn.textContent : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Enviando...';
            }

            fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(async (response) => {
                const data = await response.json().catch(() => ({}));

                // CORRECCIÓN [Nivel Menor]:
                // Error: Si Laravel devuelve errores de validación (HTTP 422), la propiedad 'data.errors' contiene
                //        los detalles de cada campo, pero el código original solo leía data.message dejando al usuario sin saber qué falló.
                // Solución: Manejar la respuesta verificando response.ok y formatear todos los mensajes de error de data.errors.
                if (response.ok && data.success) {
                    alert('¡Gracias! Tu solicitud de empleo ha sido enviada correctamente.');
                    form.reset();
                    // Restablecer la fecha automática y el primer paso
                    const todayDate = new Date().toISOString().split('T')[0];
                    const fechaInput = document.getElementById('fecha_solicitud');
                    if (fechaInput) {
                        fechaInput.value = todayDate;
                    }
                    currentStep = 0;
                    updateStepVisibility();
                } else {
                    let errorMsg = data.message || 'No se pudo registrar la solicitud.';
                    if (data.errors) {
                        const errorList = Object.values(data.errors).flat().join('\n• ');
                        errorMsg += '\n\nDetalles:\n• ' + errorList;
                    }
                    alert(errorMsg);
                }
            })
            .catch(error => {
                alert('Error al conectar con el servidor: ' + error.message);
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                }
            });
        });