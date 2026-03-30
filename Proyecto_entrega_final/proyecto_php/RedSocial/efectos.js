/* =========================================
   EFECTOS.JS — Red Social
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {

  /* ── 1. RIPPLE en botones ── */
  document.querySelectorAll('.btn, button[type="submit"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
      const ripple = document.createElement('span');
      const rect   = this.getBoundingClientRect();
      const size   = Math.max(rect.width, rect.height);
      ripple.style.cssText = `
        position:absolute; border-radius:50%;
        width:${size}px; height:${size}px;
        left:${e.clientX - rect.left - size/2}px;
        top:${e.clientY - rect.top  - size/2}px;
        background:rgba(255,255,255,0.4);
        transform:scale(0); animation:rippleAnim 0.6s ease-out forwards;
        pointer-events:none;
      `;
      this.style.position = 'relative';
      this.style.overflow = 'hidden';
      this.appendChild(ripple);
      setTimeout(() => ripple.remove(), 700);
    });
  });

  if (!document.querySelector('#ripple-style')) {
    const s = document.createElement('style');
    s.id = 'ripple-style';
    s.textContent = `@keyframes rippleAnim{to{transform:scale(3);opacity:0}}`;
    document.head.appendChild(s);
  }

  /* ── 2. HOVER LIFT en cards ── */
  document.querySelectorAll('.card, .post-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
      card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
    });
  });

  /* ── 3. SCROLL REVEAL ── */
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.card, .post-card, .reveal').forEach((el, i) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(25px)';
    el.style.transition = `opacity 0.5s ease ${i * 0.08}s, transform 0.5s ease ${i * 0.08}s`;
    observer.observe(el);
  });

  /* ── 4. PARTÍCULAS CURSOR ── */
  const colors = ['#c084fc','#67e8f9','#f9a8d4','#86efac','#fde68a'];
  document.addEventListener('mousemove', (e) => {
    if (Math.random() > 0.85) {
      const p = document.createElement('div');
      const size = Math.random() * 8 + 4;
      p.style.cssText = `
        position:fixed; pointer-events:none; z-index:9999; border-radius:50%;
        width:${size}px; height:${size}px;
        background:${colors[Math.floor(Math.random()*colors.length)]};
        left:${e.clientX - size/2}px; top:${e.clientY - size/2}px;
        opacity:0.9; transition:all 0.8s ease;
      `;
      document.body.appendChild(p);
      requestAnimationFrame(() => {
        p.style.transform = `translate(${(Math.random()-0.5)*60}px, ${-Math.random()*60}px) scale(0)`;
        p.style.opacity = '0';
      });
      setTimeout(() => p.remove(), 900);
    }
  });

  /* ── 5. NOTIFICACIÓN TOAST ── */
  window.showToast = function(msg, type = 'success') {
    const toast = document.createElement('div');
    const icons = { success:'✅', error:'❌', info:'ℹ️', warning:'⚠️' };
    toast.style.cssText = `
      position:fixed; bottom:90px; right:24px; z-index:9999;
      background:white; border-radius:12px; padding:14px 20px;
      box-shadow:0 8px 30px rgba(0,0,0,0.15);
      font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:500;
      border-left:4px solid ${type==='success'?'#10b981':type==='error'?'#ef4444':'#3b82f6'};
      transform:translateX(120%); transition:transform 0.4s cubic-bezier(0.4,0,0.2,1);
      display:flex; align-items:center; gap:10px; color:#374151;
      max-width:320px;
    `;
    toast.innerHTML = `<span>${icons[type]||'💬'}</span><span>${msg}</span>`;
    document.body.appendChild(toast);
    requestAnimationFrame(() => { toast.style.transform = 'translateX(0)'; });
    setTimeout(() => {
      toast.style.transform = 'translateX(120%)';
      setTimeout(() => toast.remove(), 400);
    }, 3500);
  };

  /* ── 6. CONTADOR DE CARACTERES en textarea ── */
  document.querySelectorAll('textarea[maxlength]').forEach(ta => {
    const max     = ta.getAttribute('maxlength');
    const counter = document.createElement('div');
    counter.style.cssText = 'text-align:right; font-size:0.78rem; color:#9ca3af; margin-top:4px;';
    counter.textContent = `0 / ${max}`;
    ta.parentNode.insertBefore(counter, ta.nextSibling);
    ta.addEventListener('input', () => {
      counter.textContent = `${ta.value.length} / ${max}`;
      counter.style.color = ta.value.length > max * 0.9 ? '#ef4444' : '#9ca3af';
    });
  });

  /* ── 7. CONFIRM ELEGANTE para eliminar ── */
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function(e) {
      e.preventDefault();
      const msg  = this.dataset.confirm || '¿Estás seguro?';
      const href = this.href;
      const overlay = document.createElement('div');
      overlay.style.cssText = `
        position:fixed; inset:0; background:rgba(0,0,0,0.4);
        z-index:10000; display:flex; align-items:center; justify-content:center;
        backdrop-filter:blur(4px); animation:fadeIn 0.2s ease;
      `;
      overlay.innerHTML = `
        <div style="background:white; border-radius:20px; padding:32px; max-width:340px; width:90%;
                    text-align:center; animation:scaleIn 0.3s ease; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
          <div style="font-size:3rem; margin-bottom:12px;">⚠️</div>
          <h3 style="color:#1f2937; margin-bottom:10px;">¿Confirmar acción?</h3>
          <p style="color:#6b7280; margin-bottom:24px; font-size:0.9rem;">${msg}</p>
          <div style="display:flex; gap:12px; justify-content:center;">
            <button id="confirmNo"  style="padding:10px 24px; border-radius:50px; border:2px solid #e5e7eb;
              background:white; cursor:pointer; font-weight:600; color:#374151;">Cancelar</button>
            <button id="confirmSi"  style="padding:10px 24px; border-radius:50px; border:none;
              background:linear-gradient(135deg,#ef4444,#dc2626); color:white; cursor:pointer; font-weight:600;
              box-shadow:0 4px 15px rgba(239,68,68,0.4);">Sí, eliminar</button>
          </div>
        </div>
      `;
      if (!document.querySelector('#confirm-style')) {
        const cs = document.createElement('style');
        cs.id = 'confirm-style';
        cs.textContent = `@keyframes fadeIn{from{opacity:0}to{opacity:1}}@keyframes scaleIn{from{transform:scale(0.7);opacity:0}to{transform:scale(1);opacity:1}}`;
        document.head.appendChild(cs);
      }
      document.body.appendChild(overlay);
      overlay.querySelector('#confirmNo').onclick = () => overlay.remove();
      overlay.querySelector('#confirmSi').onclick = () => { window.location.href = href; };
    });
  });

  /* ── 8. TRANSICIONES DE PÁGINA ── */
  document.querySelectorAll('a:not([target="_blank"]):not([href^="#"]):not([href^="mailto"])').forEach(link => {
    link.addEventListener('click', function(e) {
      if (this.href && !this.href.includes('javascript')) {
        const overlay = document.createElement('div');
        overlay.style.cssText = `
          position:fixed; inset:0; background:linear-gradient(135deg,#8b5cf6,#ec4899);
          z-index:99999; opacity:0; transition:opacity 0.3s ease; pointer-events:none;
        `;
        document.body.appendChild(overlay);
        requestAnimationFrame(() => { overlay.style.opacity = '1'; });
      }
    });
  });

  /* ── 9. INPUT FOCUS GLOW ── */
  document.querySelectorAll('input, textarea, select').forEach(el => {
    el.addEventListener('focus', () => {
      el.style.boxShadow = '0 0 0 4px rgba(139,92,246,0.15)';
    });
    el.addEventListener('blur', () => {
      el.style.boxShadow = '';
    });
  });

  /* ── 10. PANDA EASTER EGG ── */
  const panda = document.querySelector('.panda-deco');
  if (panda) {
    let clicks = 0;
    panda.addEventListener('click', () => {
      clicks++;
      const msgs = ['🐼 ¡Hola!','💜 ¡Soy el guardián!','🌟 ¡Sigue publicando!','🎉 ¡Eres genial!'];
      showToast(msgs[clicks % msgs.length], 'info');
      panda.style.animation = 'none';
      panda.style.transform = 'scale(1.3) rotate(10deg)';
      setTimeout(() => {
        panda.style.transform = '';
        panda.style.animation = 'float 3.5s ease-in-out infinite';
      }, 400);
    });
  }

});
