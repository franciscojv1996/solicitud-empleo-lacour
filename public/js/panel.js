/**
 * Lógica interactiva para el Panel de Control de Solicitudes (Grupo Lacour)
 */

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('candidateModal');
    const modalCloseBtn = document.getElementById('modalCloseBtn');
    const modalCloseFooterBtn = document.getElementById('modalCloseFooterBtn');
    const printBtn = document.getElementById('printExpedienteBtn');
    const tabButtons = document.querySelectorAll('.modal-tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    // Cerrar modal
    function closeModal() {
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);
    if (modalCloseFooterBtn) modalCloseFooterBtn.addEventListener('click', closeModal);

    // Cerrar modal al hacer clic en el backdrop
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Cerrar modal con tecla ESC
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
            closeModal();
        }
    });

    // Manejo de pestañas del modal
    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            tabButtons.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            btn.classList.add('active');
            const targetPaneId = btn.getAttribute('data-tab');
            const targetPane = document.getElementById(targetPaneId);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    // Imprimir expediente
    if (printBtn) {
        printBtn.addEventListener('click', () => {
            window.print();
        });
    }

    // Botones de acción "Ver Detalle" en la tabla
    document.querySelectorAll('.btn-ver-detalle').forEach(btn => {
        btn.addEventListener('click', () => {
            const rawData = btn.getAttribute('data-solicitud');
            if (!rawData) return;

            try {
                const s = JSON.parse(rawData);
                populateModal(s);
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            } catch (err) {
                console.error('Error al parsear datos de la solicitud:', err);
            }
        });
    });

    // Función para rellenar los datos del modal con la solicitud seleccionada
    function populateModal(s) {
        // Título del modal
        document.getElementById('modalCandidateName').textContent = s.nombres || 'Sin nombre';
        document.getElementById('modalCandidateSub').textContent = 
            `Cédula: ${s.cedula_tipo || 'V'}-${s.cedula_numero || ''} | Cargo: ${s.cargo_aspira || 'No especificado'} | Fecha: ${s.fecha_solicitud || ''}`;

        // 1. Datos Personales y del Cargo
        setText('m_cargo', s.cargo_aspira);
        setText('m_sueldo', s.sueldo_aspira ? s.sueldo_aspira : 'No indicado');
        setText('m_empleado_actual', s.empleado_actual || 'No');
        setText('m_fecha_solicitud', s.fecha_solicitud);
        setText('m_cedula', `${s.cedula_tipo || 'V'}-${s.cedula_numero || ''}`);
        setText('m_nombres', s.nombres);
        setText('m_sexo', s.sexo);
        setText('m_estado_civil', s.estado_civil);
        setText('m_nacimiento', `${s.lugar_nacimiento || ''} ${s.fecha_nacimiento ? '(' + s.fecha_nacimiento + ')' : ''}`);
        setText('m_edad', s.edad ? `${s.edad} años` : 'No indicada');
        setText('m_nacionalidad', s.nacionalidad);
        setText('m_telefono', s.telefono);
        setText('m_correo', s.correo);
        setText('m_direccion', s.direccion);
        setText('m_otra_direccion', s.otra_direccion || 'Ninguna');
        setText('m_vehiculo', s.vehiculo || 'No');
        setText('m_licencia', s.licencia === 'Si' ? `Sí (${s.licencia_detalles || 'Sin detalles'})` : 'No');
        setText('m_certificado_medico', s.certificado_medico ? `${s.certificado_medico} (Vcto: ${s.fecha_vcto_medico || 'N/A'})` : 'No indicado');
        setText('m_tallas', `Camisa: ${s.talla_camisa || '-'} | Pantalón: ${s.talla_pantalon || '-'} | Botas: ${s.talla_botas || '-'}`);
        setText('m_medidas', `Estatura: ${s.estatura || '-'} | Peso: ${s.peso ? s.peso + ' Kg' : '-'}`);

        // 2. Familiares
        const famTbody = document.getElementById('m_familiares_tbody');
        famTbody.innerHTML = '';
        if (s.familiares && s.familiares.length > 0) {
            s.familiares.forEach(f => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${escapeHtml(f.parentesco || '-')}</strong></td>
                    <td>${escapeHtml(f.nombre || '-')}</td>
                    <td>${escapeHtml(f.cedula || '-')}</td>
                    <td>${escapeHtml(f.telefono || '-')}</td>
                    <td>${escapeHtml(f.edad ? f.edad + ' años' : '-')}</td>
                `;
                famTbody.appendChild(tr);
            });
        } else {
            famTbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#94a3b8;">No se registraron datos familiares.</td></tr>';
        }

        // 3. Educación
        const eduTbody = document.getElementById('m_educacion_tbody');
        eduTbody.innerHTML = '';
        if (s.educacion && s.educacion.length > 0) {
            s.educacion.forEach(e => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${escapeHtml(e.nivel || '-')}</strong></td>
                    <td>${escapeHtml(e.instituto || '-')}</td>
                    <td>${escapeHtml(e.anos_cursados ? e.anos_cursados + ' años' : '-')}</td>
                    <td>${escapeHtml(e.fecha_desde || '')} a ${escapeHtml(e.fecha_hasta || '')}</td>
                    <td>${e.obtuvo_titulo === 'Si' ? '✅ Sí' : (e.obtuvo_titulo || '-')}</td>
                    <td>${escapeHtml(e.especialidad || '-')}</td>
                `;
                eduTbody.appendChild(tr);
            });
        } else {
            eduTbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#94a3b8;">No se registraron estudios.</td></tr>';
        }

        // 4. Experiencia Laboral
        const expContainer = document.getElementById('m_experiencia_container');
        expContainer.innerHTML = '';
        if (s.experiencia_laboral && s.experiencia_laboral.length > 0) {
            s.experiencia_laboral.forEach((exp, idx) => {
                const div = document.createElement('div');
                div.className = 'detail-item';
                div.style.marginBottom = '12px';
                div.innerHTML = `
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <h4 style="color:#003366; font-size:14px;">#${idx + 1} - ${escapeHtml(exp.empresa || 'Empresa')}</h4>
                        <span style="font-size:12px; color:#64748b;">${escapeHtml(exp.fecha_ingreso || '')} a ${escapeHtml(exp.fecha_egreso || '')}</span>
                    </div>
                    <div class="detail-grid" style="margin-bottom:8px;">
                        <div><div class="detail-label">Cargo:</div><div class="detail-value">${escapeHtml(exp.cargo_desempenado || '-')}</div></div>
                        <div><div class="detail-label">Jefe Inmediato:</div><div class="detail-value">${escapeHtml(exp.jefe_inmediato || '-')} (${escapeHtml(exp.telefono || '-')})</div></div>
                        <div><div class="detail-label">Sueldo Inicial / Final:</div><div class="detail-value">${escapeHtml(exp.sueldo_inicial || '-')} / ${escapeHtml(exp.sueldo_final || '-')}</div></div>
                        <div><div class="detail-label">Motivo Retiro:</div><div class="detail-value">${escapeHtml(exp.motivo_retiro || '-')}</div></div>
                    </div>
                    <div>
                        <div class="detail-label">Labores Realizadas:</div>
                        <p style="font-size:13px; color:#334155; margin-top:4px;">${escapeHtml(exp.labores_realizadas || 'No descritas.')}</p>
                    </div>
                `;
                expContainer.appendChild(div);
            });
        } else {
            expContainer.innerHTML = '<p style="text-align:center; color:#94a3b8; padding:20px;">No se registró experiencia laboral previa.</p>';
        }

        // 5. Referencias Personales
        const refTbody = document.getElementById('m_referencias_tbody');
        refTbody.innerHTML = '';
        if (s.referencias_personales && s.referencias_personales.length > 0) {
            s.referencias_personales.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${escapeHtml(r.nombre || '-')}</strong></td>
                    <td>${escapeHtml(r.empresa || '-')}</td>
                    <td>${escapeHtml(r.ocupacion || '-')}</td>
                    <td>${escapeHtml(r.telefono || '-')}</td>
                    <td>${escapeHtml(r.tiempo_conocido || '-')}</td>
                `;
                refTbody.appendChild(tr);
            });
        } else {
            refTbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#94a3b8;">No se registraron referencias personales.</td></tr>';
        }

        // 6. Documentos
        const docContainer = document.getElementById('m_documentos_container');
        docContainer.innerHTML = '';
        const doc = s.documentos;
        if (doc && (doc.cv_ruta || doc.cedula_ruta || doc.rif_ruta)) {
            let html = '<div style="display:flex; flex-wrap:wrap; gap:12px;">';
            if (doc.cv_ruta) {
                html += `<a href="/storage/${doc.cv_ruta}" target="_blank" class="btn btn-primary" style="background:#0d9488;">📄 Ver Currículum Vitae (CV)</a>`;
            }
            if (doc.cedula_ruta) {
                html += `<a href="/storage/${doc.cedula_ruta}" target="_blank" class="btn btn-secondary">🪪 Ver Copia de Cédula</a>`;
            }
            if (doc.rif_ruta) {
                html += `<a href="/storage/${doc.rif_ruta}" target="_blank" class="btn btn-secondary">📋 Ver Copia de RIF</a>`;
            }
            html += '</div>';
            docContainer.innerHTML = html;
        } else {
            docContainer.innerHTML = '<p style="color:#94a3b8;">El solicitante no adjuntó ningún archivo digital.</p>';
        }

        // Activar la primera pestaña por defecto
        if (tabButtons[0]) tabButtons[0].click();
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value || 'No indicado';
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }
});
