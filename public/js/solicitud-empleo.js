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
        document.getElementById('downloadBtn').addEventListener('click', () => {
            window.location.href = 'public/doc/descargar_formulario.pdf';
        });

        // --- 6. Validación del Formulario ---
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

            const formData = new FormData(document.getElementById('solicitudForm'));

            fetch('backend/guardar_solicitud.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('¡Gracias! Tu solicitud de empleo ha sido enviada correctamente.');
                } else {
                    alert('No se pudo registrar la solicitud: ' + (data.message || 'Error desconocido.'));
                }
            })
            .catch(error => {
                alert('Error al conectar con el servidor: ' + error);
            });
        });