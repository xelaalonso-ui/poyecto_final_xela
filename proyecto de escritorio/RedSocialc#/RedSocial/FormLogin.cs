using System;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Windows.Forms;

namespace RedSocial
{
    public partial class FormLogin : Form
    {
        public FormLogin()
        {
            InitializeComponent();
            ConfigurarVentana();
        }

        private void ConfigurarVentana()
        {
          

            // Fondo animado
            FondoAnimado fondo = new FondoAnimado();
            fondo.SendToBack();
            this.Controls.Add(fondo);
            fondo.SendToBack();

            // Animar entrada con fade
            this.Opacity = 0;
            // Cargar GIF
            try {
                picGato.Image = System.Drawing.Image.FromFile("An.gif"); 
            }
            
            catch { }

            Timer t = new Timer { Interval = 15 };
            t.Tick += (s, e) => { if (this.Opacity < 1) this.Opacity += 0.07; else t.Stop(); };
            t.Start();
        }

        private void btnLogin_Click(object sender, EventArgs e)
        {
            lblError.Visible = false;

            bool ok = true;
            if (!ValidateTextbox.ValidarRequerido(tbEmail, lblError, "El email es obligatorio"))
            {
                ok = false;
            }
            else if (!ValidateTextbox.EsEmail(tbEmail))
            {
                ValidateTextbox.MarcarError(tbEmail);
                lblError.Text = "Email no valido";
                lblError.Visible = true;
                ok = false;
            }
            else
            {
                ValidateTextbox.MarcarOk(tbEmail);
            }

            if (ok && !ValidateTextbox.ValidarRequerido(tbPassword, lblError, "La contrasena es obligatoria"))
            {
                ok = false;
            }

            if (!ok)
            {
                return;
            }

            if (DataStore.Login(tbEmail.Text.Trim(), tbPassword.Text))
            {
                // Mostrar bienvenida con nombre del usuario y animacion
                string nombre = DataStore.UsuarioActual.Username;
                lblBienvenido.Text      = "Bienvenido/a, @" + nombre + "!";
                lblBienvenido.ForeColor = Color.FromArgb(139, 92, 246);
                lblBienvenido.Font      = new Font("Segoe UI", 11, FontStyle.Bold);

                // Pequeña pausa para que el usuario vea el saludo, luego abre principal
                Timer tWelcome = new Timer {
                    Interval = 1200 
                };
                tWelcome.Tick += (s2, e2) => {
                    tWelcome.Stop();
                    FormPrincipal fp = new FormPrincipal();
                    fp.Show();
                    this.Hide();
                    fp.FormClosed += (s3, e3) => this.Close();
                };
                tWelcome.Start();
            }
            else
            {
                lblError.Text    = "Email o contrasena incorrectos";
                lblError.Visible = true;
                ValidateTextbox.MarcarError(tbEmail);
                ValidateTextbox.MarcarError(tbPassword);
            }
        }

        private void lnkRegistro_LinkClicked(object sender, LinkLabelLinkClickedEventArgs e)
        {
            FormRegistro fr = new FormRegistro();
            fr.Show();
            this.Hide();
            fr.FormClosed += (s, ev) => this.Show();
        }

        private void tbEmail_TextChanged(object sender, EventArgs e)
        {
            ValidateTextbox.MarcarOk(tbEmail);
            lblError.Visible   = false;
            lblBienvenido.Text = "Bienvenido/a!";
            lblBienvenido.Font = new Font("Segoe UI", 10, FontStyle.Italic);
            lblBienvenido.ForeColor = Color.FromArgb(107, 79, 160);
        }

        private void tbPassword_TextChanged(object sender, EventArgs e)
        {
            ValidateTextbox.MarcarOk(tbPassword);
            lblError.Visible = false;
        }


    }
}
