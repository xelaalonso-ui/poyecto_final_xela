namespace RedSocial
{
    partial class FormLogin
    {
        private System.ComponentModel.IContainer components = null;
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null)) components.Dispose();
            base.Dispose(disposing);
        }

        private System.Windows.Forms.TextBox    tbEmail;
        private System.Windows.Forms.TextBox    tbPassword;
        private System.Windows.Forms.Button     btnLogin;
        private System.Windows.Forms.Label      lblError;
        private System.Windows.Forms.LinkLabel  lnkRegistro;
        private System.Windows.Forms.Label      lblEmail;
        private System.Windows.Forms.Label      lblPass;
        private System.Windows.Forms.PictureBox picGato;
        private System.Windows.Forms.Label      lblBienvenido;

        private void InitializeComponent()
        {
            this.tbEmail = new System.Windows.Forms.TextBox();
            this.tbPassword = new System.Windows.Forms.TextBox();
            this.btnLogin = new System.Windows.Forms.Button();
            this.lblError = new System.Windows.Forms.Label();
            this.lnkRegistro = new System.Windows.Forms.LinkLabel();
            this.lblEmail = new System.Windows.Forms.Label();
            this.lblPass = new System.Windows.Forms.Label();
            this.picGato = new System.Windows.Forms.PictureBox();
            this.lblBienvenido = new System.Windows.Forms.Label();
            ((System.ComponentModel.ISupportInitialize)(this.picGato)).BeginInit();
            this.SuspendLayout();
            // 
            // tbEmail
            // 
            this.tbEmail.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(250)))), ((int)(((byte)(245)))), ((int)(((byte)(255)))));
            this.tbEmail.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.tbEmail.Font = new System.Drawing.Font("Segoe UI", 10F);
            this.tbEmail.ForeColor = System.Drawing.Color.FromArgb(((int)(((byte)(31)))), ((int)(((byte)(10)))), ((int)(((byte)(60)))));
            this.tbEmail.Location = new System.Drawing.Point(40, 172);
            this.tbEmail.Name = "tbEmail";
            this.tbEmail.Size = new System.Drawing.Size(360, 30);
            this.tbEmail.TabIndex = 3;
            this.tbEmail.TextChanged += new System.EventHandler(this.tbEmail_TextChanged);
            // 
            // tbPassword
            // 
            this.tbPassword.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(250)))), ((int)(((byte)(245)))), ((int)(((byte)(255)))));
            this.tbPassword.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.tbPassword.Font = new System.Drawing.Font("Segoe UI", 10F);
            this.tbPassword.ForeColor = System.Drawing.Color.FromArgb(((int)(((byte)(31)))), ((int)(((byte)(10)))), ((int)(((byte)(60)))));
            this.tbPassword.Location = new System.Drawing.Point(40, 240);
            this.tbPassword.Name = "tbPassword";
            this.tbPassword.PasswordChar = '*';
            this.tbPassword.Size = new System.Drawing.Size(360, 30);
            this.tbPassword.TabIndex = 5;
            this.tbPassword.TextChanged += new System.EventHandler(this.tbPassword_TextChanged);
            // 
            // btnLogin
            // 
            this.btnLogin.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(139)))), ((int)(((byte)(92)))), ((int)(((byte)(246)))));
            this.btnLogin.Cursor = System.Windows.Forms.Cursors.Hand;
            this.btnLogin.FlatAppearance.BorderSize = 0;
            this.btnLogin.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnLogin.Font = new System.Drawing.Font("Segoe UI", 11F, System.Drawing.FontStyle.Bold);
            this.btnLogin.ForeColor = System.Drawing.Color.White;
            this.btnLogin.Location = new System.Drawing.Point(40, 314);
            this.btnLogin.Name = "btnLogin";
            this.btnLogin.Size = new System.Drawing.Size(360, 46);
            this.btnLogin.TabIndex = 7;
            this.btnLogin.Text = "Iniciar sesion";
            this.btnLogin.UseVisualStyleBackColor = false;
            this.btnLogin.Click += new System.EventHandler(this.btnLogin_Click);
            // 
            // lblError
            // 
            this.lblError.BackColor = System.Drawing.Color.Transparent;
            this.lblError.Font = new System.Drawing.Font("Segoe UI", 9F);
            this.lblError.ForeColor = System.Drawing.Color.FromArgb(((int)(((byte)(239)))), ((int)(((byte)(68)))), ((int)(((byte)(68)))));
            this.lblError.Location = new System.Drawing.Point(40, 284);
            this.lblError.Name = "lblError";
            this.lblError.Size = new System.Drawing.Size(360, 20);
            this.lblError.TabIndex = 6;
            this.lblError.Visible = false;
            // 
            // lnkRegistro
            // 
            this.lnkRegistro.Font = new System.Drawing.Font("Segoe UI", 9F);
            this.lnkRegistro.LinkColor = System.Drawing.Color.FromArgb(((int)(((byte)(139)))), ((int)(((byte)(92)))), ((int)(((byte)(246)))));
            this.lnkRegistro.Location = new System.Drawing.Point(40, 374);
            this.lnkRegistro.Name = "lnkRegistro";
            this.lnkRegistro.Size = new System.Drawing.Size(360, 22);
            this.lnkRegistro.TabIndex = 8;
            this.lnkRegistro.TabStop = true;
            this.lnkRegistro.Text = "No tienes cuenta? Registrate aqui";
            this.lnkRegistro.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            this.lnkRegistro.LinkClicked += new System.Windows.Forms.LinkLabelLinkClickedEventHandler(this.lnkRegistro_LinkClicked);
            // 
            // lblEmail
            // 
            this.lblEmail.BackColor = System.Drawing.Color.Transparent;
            this.lblEmail.Font = new System.Drawing.Font("Segoe UI", 9F, System.Drawing.FontStyle.Bold);
            this.lblEmail.ForeColor = System.Drawing.Color.FromArgb(((int)(((byte)(107)))), ((int)(((byte)(79)))), ((int)(((byte)(160)))));
            this.lblEmail.Location = new System.Drawing.Point(40, 150);
            this.lblEmail.Name = "lblEmail";
            this.lblEmail.Size = new System.Drawing.Size(360, 18);
            this.lblEmail.TabIndex = 2;
            this.lblEmail.Text = "Email";
            // 
            // lblPass
            // 
            this.lblPass.BackColor = System.Drawing.Color.Transparent;
            this.lblPass.Font = new System.Drawing.Font("Segoe UI", 9F, System.Drawing.FontStyle.Bold);
            this.lblPass.ForeColor = System.Drawing.Color.FromArgb(((int)(((byte)(107)))), ((int)(((byte)(79)))), ((int)(((byte)(160)))));
            this.lblPass.Location = new System.Drawing.Point(40, 218);
            this.lblPass.Name = "lblPass";
            this.lblPass.Size = new System.Drawing.Size(360, 18);
            this.lblPass.TabIndex = 4;
            this.lblPass.Text = "Contrasena";
            // 
            // picGato
            // 
            this.picGato.BackColor = System.Drawing.Color.Transparent;
            this.picGato.Location = new System.Drawing.Point(155, 10);
            this.picGato.Name = "picGato";
            this.picGato.Size = new System.Drawing.Size(130, 100);
            this.picGato.SizeMode = System.Windows.Forms.PictureBoxSizeMode.Zoom;
            this.picGato.TabIndex = 0;
            this.picGato.TabStop = false;
            // 
            // lblBienvenido
            // 
            this.lblBienvenido.BackColor = System.Drawing.Color.Transparent;
            this.lblBienvenido.Font = new System.Drawing.Font("Segoe UI", 10F, System.Drawing.FontStyle.Italic);
            this.lblBienvenido.ForeColor = System.Drawing.Color.FromArgb(((int)(((byte)(107)))), ((int)(((byte)(79)))), ((int)(((byte)(160)))));
            this.lblBienvenido.Location = new System.Drawing.Point(40, 115);
            this.lblBienvenido.Name = "lblBienvenido";
            this.lblBienvenido.Size = new System.Drawing.Size(360, 22);
            this.lblBienvenido.TabIndex = 1;
            this.lblBienvenido.Text = "Bienvenido/a!";
            this.lblBienvenido.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // FormLogin
            // 
            this.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(243)))), ((int)(((byte)(232)))), ((int)(((byte)(255)))));
            this.ClientSize = new System.Drawing.Size(422, 393);
            this.Controls.Add(this.picGato);
            this.Controls.Add(this.lblBienvenido);
            this.Controls.Add(this.lblEmail);
            this.Controls.Add(this.tbEmail);
            this.Controls.Add(this.lblPass);
            this.Controls.Add(this.tbPassword);
            this.Controls.Add(this.lblError);
            this.Controls.Add(this.btnLogin);
            this.Controls.Add(this.lnkRegistro);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.MaximizeBox = false;
            this.Name = "FormLogin";
            this.SizeGripStyle = System.Windows.Forms.SizeGripStyle.Show;
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Red Social";
            ((System.ComponentModel.ISupportInitialize)(this.picGato)).EndInit();
            this.ResumeLayout(false);
            this.PerformLayout();

        }
    }
}
