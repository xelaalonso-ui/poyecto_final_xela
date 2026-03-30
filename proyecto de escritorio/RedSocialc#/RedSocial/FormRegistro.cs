using System;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Windows.Forms;

namespace RedSocial
{
    public partial class FormRegistro : Form
    {
        public FormRegistro()
        {
            InitializeComponent();

            // Añadir fondo animado
            FondoAnimado fondo = new FondoAnimado();
            this.Controls.Add(fondo);
            fondo.SendToBack();

            // Animación de aparición del formulario
            Timer timerAnimacion = new Timer { Interval = 15 };

            timerAnimacion.Tick += (s, e) =>
            {
                if (this.Opacity < 1)
                {
                    this.Opacity += 0.07;
                }

                else
                {
                    timerAnimacion.Stop();
                }

            };

            timerAnimacion.Start();
        }

        private void btnRegistrar_Click(object sender, EventArgs e)
        {
            lblError.Visible = false;
            bool formularioValido = true;

            // Validación usuario
            if (!Vadilar_text.ValidarRequerido(tbUsername, lblError, "El usuario es obligatorio"))
            {
                formularioValido = false;
            }
            else if (!Vadilar_text.LongitudMinima(tbUsername, 3))
            {
                Vadilar_text.MarcarError(tbUsername);
                lblError.Text = "El usuario debe tener al menos 3 caracteres";
                lblError.Visible = true;
                formularioValido = false;
            }

            // Validación email
            if (formularioValido && !Vadilar_text.ValidarRequerido(tbEmail, lblError, "El email es obligatorio"))
            {
                formularioValido = false;
            }
            else if (formularioValido && !Vadilar_text.EsEmail(tbEmail))
            {
                Vadilar_text.MarcarError(tbEmail);
                lblError.Text = "Email no válido";
                lblError.Visible = true;
                formularioValido = false;
            }

            // Validación contraseña
            if (formularioValido && !Vadilar_text.ValidarRequerido(tbPassword, lblError, "La contraseña es obligatoria"))
            {
                formularioValido = false;
            }
            else if (formularioValido && !Vadilar_text.LongitudMinima(tbPassword, 6))
            {
                Vadilar_text.MarcarError(tbPassword);
                lblError.Text = "La contraseña debe tener al menos 6 caracteres";
                lblError.Visible = true;
                formularioValido = false;
            }

            // Si hay errores, salir
            if (!formularioValido)
            {
                return;
            }

            // Intentar registrar usuario
            string mensajeError;

            if (Datos.Registrar(
                tbUsername.Text.Trim(),
                tbEmail.Text.Trim(),
                tbPassword.Text,
                out mensajeError))
            {
                MessageBox.Show(
                    "¡Cuenta creada correctamente!\nAhora puedes iniciar sesión.",
                    "Éxito",
                    MessageBoxButtons.OK,
                    MessageBoxIcon.Information
                );

                this.Close();
            }
            else
            {
                lblError.Text = mensajeError;
                lblError.Visible = true;
            }
        }

        // Cerrar formulario (volver a login)
        private void lnkLogin_LinkClicked(object sender, LinkLabelLinkClickedEventArgs e)
        {
            this.Close();
        }

        // Limpiar errores al escribir
        private void tbUsername_TextChanged(object sender, EventArgs e)
        {
            Vadilar_text.MarcarOk(tbUsername);
            lblError.Visible = false;
        }

        private void tbEmail_TextChanged(object sender, EventArgs e)
        {
            Vadilar_text.MarcarOk(tbEmail);
            lblError.Visible = false;
        }

        private void tbPassword_TextChanged(object sender, EventArgs e)
        {
            Vadilar_text.MarcarOk(tbPassword);
            lblError.Visible = false;
        }

        // Dibujar cabecera personalizada
        protected override void OnPaint(PaintEventArgs e)
        {
            base.OnPaint(e);

            Graphics g = e.Graphics;

            // Rectángulo superior (header)
            Rectangle rectanguloHeader = new Rectangle(0, 0, this.Width, 160);

            // Fondo degradado
            using (LinearGradientBrush brush = new LinearGradientBrush(
                rectanguloHeader,
                Color.FromArgb(124, 58, 237),
                Color.FromArgb(236, 72, 153),
                135f))
            {
                g.FillRectangle(brush, rectanguloHeader);
            }

            // Título
            using (Font fuenteTitulo = new Font("Segoe UI", 20, FontStyle.Bold))
            using (StringFormat formato = new StringFormat { Alignment = StringAlignment.Center })
            {
                g.DrawString("Crear cuenta", fuenteTitulo, Brushes.White,
                    new RectangleF(0, 50, this.Width, 46), formato);
            }

            // Subtítulo
            using (Font fuenteSubtitulo = new Font("Segoe UI", 10))
            using (StringFormat formato = new StringFormat { Alignment = StringAlignment.Center })
            {
                g.DrawString("¡Únete a la comunidad!", fuenteSubtitulo,
                    new SolidBrush(Color.FromArgb(210, 255, 255, 255)),
                    new RectangleF(0, 104, this.Width, 28), formato);
            }
        }
    }
}