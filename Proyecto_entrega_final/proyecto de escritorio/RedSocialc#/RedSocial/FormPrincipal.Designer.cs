namespace RedSocial
{
    partial class FormPrincipal
    {
        private System.ComponentModel.IContainer components = null;
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null)) components.Dispose();
            base.Dispose(disposing);
        }

        private System.Windows.Forms.Panel      pnlHeader;
        private System.Windows.Forms.Panel      pnlNav;
        private System.Windows.Forms.Button     btnFeed;
        private System.Windows.Forms.Button     btnPerfil;
        private System.Windows.Forms.Button     btnCuenta;
        private System.Windows.Forms.PictureBox picGatoNav;

        private void InitializeComponent()
        {
            this.pnlHeader = new System.Windows.Forms.Panel();
            this.pnlNav = new System.Windows.Forms.Panel();
            this.btnFeed = new System.Windows.Forms.Button();
            this.btnPerfil = new System.Windows.Forms.Button();
            this.btnCuenta = new System.Windows.Forms.Button();
            this.picGatoNav = new System.Windows.Forms.PictureBox();
            this.pnlFeed = new System.Windows.Forms.Panel();
            this.pnlPerfil = new System.Windows.Forms.Panel();
            this.pnlCuenta = new System.Windows.Forms.Panel();
            this.pnlNav.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.picGatoNav)).BeginInit();
            this.SuspendLayout();
            // 
            // pnlHeader
            // 
            this.pnlHeader.Dock = System.Windows.Forms.DockStyle.Top;
            this.pnlHeader.Location = new System.Drawing.Point(0, 0);
            this.pnlHeader.Name = "pnlHeader";
            this.pnlHeader.Size = new System.Drawing.Size(902, 60);
            this.pnlHeader.TabIndex = 4;
            // 
            // pnlNav
            // 
            this.pnlNav.BackColor = System.Drawing.Color.White;
            this.pnlNav.Controls.Add(this.btnFeed);
            this.pnlNav.Controls.Add(this.btnPerfil);
            this.pnlNav.Controls.Add(this.btnCuenta);
            this.pnlNav.Controls.Add(this.picGatoNav);
            this.pnlNav.Dock = System.Windows.Forms.DockStyle.Left;
            this.pnlNav.Location = new System.Drawing.Point(0, 60);
            this.pnlNav.Name = "pnlNav";
            this.pnlNav.Size = new System.Drawing.Size(180, 573);
            this.pnlNav.TabIndex = 3;
            // 
            // btnFeed
            // 
            this.btnFeed.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(252)))), ((int)(((byte)(231)))), ((int)(((byte)(243)))));
            this.btnFeed.Cursor = System.Windows.Forms.Cursors.Hand;
            this.btnFeed.FlatAppearance.BorderSize = 0;
            this.btnFeed.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnFeed.Font = new System.Drawing.Font("Segoe UI", 10F, System.Drawing.FontStyle.Bold);
            this.btnFeed.ForeColor = System.Drawing.Color.FromArgb(((int)(((byte)(139)))), ((int)(((byte)(92)))), ((int)(((byte)(246)))));
            this.btnFeed.Location = new System.Drawing.Point(0, 20);
            this.btnFeed.Name = "btnFeed";
            this.btnFeed.Padding = new System.Windows.Forms.Padding(16, 0, 0, 0);
            this.btnFeed.Size = new System.Drawing.Size(180, 52);
            this.btnFeed.TabIndex = 0;
            this.btnFeed.Text = "  Inicio";
            this.btnFeed.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            this.btnFeed.UseVisualStyleBackColor = false;
            // 
            // btnPerfil
            // 
            this.btnPerfil.BackColor = System.Drawing.Color.White;
            this.btnPerfil.Cursor = System.Windows.Forms.Cursors.Hand;
            this.btnPerfil.FlatAppearance.BorderSize = 0;
            this.btnPerfil.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnPerfil.Font = new System.Drawing.Font("Segoe UI", 10F);
            this.btnPerfil.ForeColor = System.Drawing.Color.FromArgb(((int)(((byte)(107)))), ((int)(((byte)(79)))), ((int)(((byte)(160)))));
            this.btnPerfil.Location = new System.Drawing.Point(0, 76);
            this.btnPerfil.Name = "btnPerfil";
            this.btnPerfil.Padding = new System.Windows.Forms.Padding(16, 0, 0, 0);
            this.btnPerfil.Size = new System.Drawing.Size(180, 52);
            this.btnPerfil.TabIndex = 1;
            this.btnPerfil.Text = "  Perfil";
            this.btnPerfil.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            this.btnPerfil.UseVisualStyleBackColor = false;
            // 
            // btnCuenta
            // 
            this.btnCuenta.BackColor = System.Drawing.Color.White;
            this.btnCuenta.Cursor = System.Windows.Forms.Cursors.Hand;
            this.btnCuenta.FlatAppearance.BorderSize = 0;
            this.btnCuenta.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnCuenta.Font = new System.Drawing.Font("Segoe UI", 10F);
            this.btnCuenta.ForeColor = System.Drawing.Color.FromArgb(((int)(((byte)(107)))), ((int)(((byte)(79)))), ((int)(((byte)(160)))));
            this.btnCuenta.Location = new System.Drawing.Point(0, 132);
            this.btnCuenta.Name = "btnCuenta";
            this.btnCuenta.Padding = new System.Windows.Forms.Padding(16, 0, 0, 0);
            this.btnCuenta.Size = new System.Drawing.Size(180, 52);
            this.btnCuenta.TabIndex = 2;
            this.btnCuenta.Text = "  Cuenta";
            this.btnCuenta.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            this.btnCuenta.UseVisualStyleBackColor = false;
            // 
            // picGatoNav
            // 
            this.picGatoNav.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left)));
            this.picGatoNav.BackColor = System.Drawing.Color.White;
            this.picGatoNav.Location = new System.Drawing.Point(10, 873);
            this.picGatoNav.Name = "picGatoNav";
            this.picGatoNav.Size = new System.Drawing.Size(160, 120);
            this.picGatoNav.SizeMode = System.Windows.Forms.PictureBoxSizeMode.Zoom;
            this.picGatoNav.TabIndex = 3;
            this.picGatoNav.TabStop = false;
            // 
            // pnlFeed
            // 
            this.pnlFeed.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.pnlFeed.AutoScroll = true;
            this.pnlFeed.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(253)))), ((int)(((byte)(244)))), ((int)(((byte)(255)))));
            this.pnlFeed.Location = new System.Drawing.Point(180, 60);
            this.pnlFeed.Name = "pnlFeed";
            this.pnlFeed.Size = new System.Drawing.Size(820, 480);
            this.pnlFeed.TabIndex = 0;
            // 
            // pnlPerfil
            // 
            this.pnlPerfil.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.pnlPerfil.AutoScroll = true;
            this.pnlPerfil.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(253)))), ((int)(((byte)(244)))), ((int)(((byte)(255)))));
            this.pnlPerfil.Location = new System.Drawing.Point(180, 60);
            this.pnlPerfil.Name = "pnlPerfil";
            this.pnlPerfil.Size = new System.Drawing.Size(820, 480);
            this.pnlPerfil.TabIndex = 1;
            this.pnlPerfil.Visible = false;
            // 
            // pnlCuenta
            // 
            this.pnlCuenta.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.pnlCuenta.AutoScroll = true;
            this.pnlCuenta.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(253)))), ((int)(((byte)(244)))), ((int)(((byte)(255)))));
            this.pnlCuenta.Location = new System.Drawing.Point(180, 60);
            this.pnlCuenta.Name = "pnlCuenta";
            this.pnlCuenta.Size = new System.Drawing.Size(820, 480);
            this.pnlCuenta.TabIndex = 2;
            this.pnlCuenta.Visible = false;
            // 
            // FormPrincipal
            // 
            this.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(243)))), ((int)(((byte)(232)))), ((int)(((byte)(255)))));
            this.ClientSize = new System.Drawing.Size(902, 633);
            this.Controls.Add(this.pnlFeed);
            this.Controls.Add(this.pnlPerfil);
            this.Controls.Add(this.pnlCuenta);
            this.Controls.Add(this.pnlNav);
            this.Controls.Add(this.pnlHeader);
            this.MinimumSize = new System.Drawing.Size(860, 600);
            this.Name = "FormPrincipal";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Mi Red Social";
            this.pnlNav.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.picGatoNav)).EndInit();
            this.ResumeLayout(false);

        }
    }
}
