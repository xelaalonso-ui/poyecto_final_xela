using System;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Windows.Forms;
using System.Linq;
using System.Diagnostics;

namespace RedSocial
{
    public partial class FormPrincipal : Form
    {
        private Panel pnlFeed;
        private Panel pnlPerfil;
        private Panel pnlCuenta;

        public FormPrincipal()
        {
            InitializeComponent();


            FondoAnimado fondo = new FondoAnimado();
            this.Controls.Add(fondo);
            fondo.SendToBack();

            this.Opacity = 0;
            Timer t = new Timer { Interval = 15 };
            t.Tick += (s, e) => { if (this.Opacity < 1) this.Opacity += 0.08; else t.Stop(); };
            t.Start();

            this.btnFeed.Click += new System.EventHandler(this.btnFeed_Click);
            this.btnPerfil.Click += new System.EventHandler(this.btnPerfil_Click);
            this.btnCuenta.Click += new System.EventHandler(this.btnCuenta_Click);
            this.pnlHeader.Paint += new System.Windows.Forms.PaintEventHandler(this.pnlHeader_Paint);
            this.pnlNav.Paint += new System.Windows.Forms.PaintEventHandler(this.pnlNav_Paint);
            this.pnlNav.SizeChanged += new System.EventHandler(this.pnlNav_SizeChanged);
            this.SizeChanged += new System.EventHandler(this.FormPrincipal_SizeChanged);

            try { picGatoNav.Image = System.Drawing.Image.FromFile("gato1.gif"); } catch { }

            CargarFeed();
        }

        private void CargarFeed()
        {
            pnlFeed.Controls.Clear();
            int y = 10;

            Panel cardPub = CrearCard(pnlFeed.Width - 20, 110, 10, y);

            TextBox tbPub = new TextBox
            {
                Left = 14,
                Top = 12,
                Width = cardPub.Width - 130,
                Height = 60,
                Multiline = true,
                ScrollBars = ScrollBars.None,
                Font = new Font("Segoe UI", 10f),
                BackColor = Color.FromArgb(250, 245, 255),
                ForeColor = Color.FromArgb(176, 160, 204),
                BorderStyle = BorderStyle.None,
                Text = "Que quieres compartir?"
            };

            tbPub.Enter += (s, e) =>
            {
                if (tbPub.Text == "Que quieres compartir?")
                {
                    tbPub.Text = "";
                    tbPub.ForeColor = Color.FromArgb(31, 10, 60);
                }
            };
            tbPub.Leave += (s, e) =>
            {
                if (string.IsNullOrWhiteSpace(tbPub.Text))
                {
                    tbPub.Text = "Que quieres compartir?";
                    tbPub.ForeColor = Color.FromArgb(176, 160, 204);
                }
            };

            Button btnPub = new Button
            {
                Text = "Publicar",
                Left = cardPub.Width - 118,
                Top = 30,
                Width = 100,
                Height = 40,
                Font = new Font("Segoe UI", 10f, FontStyle.Bold),
                BackColor = Color.FromArgb(139, 92, 246),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnPub.FlatAppearance.BorderSize = 0;
            btnPub.Click += (s, e) =>
            {
                if (!string.IsNullOrWhiteSpace(tbPub.Text) && tbPub.Text != "Que quieres compartir?")
                {
                    Datos.Publicar(tbPub.Text.Trim());
                    CargarFeed();
                    try
                    {

                        picGatoNav.Image = System.Drawing.Image.FromFile("gato1.gif");
                    }


                    catch { }
                }
            };

            cardPub.Controls.Add(tbPub);
            cardPub.Controls.Add(btnPub);
            pnlFeed.Controls.Add(cardPub);
            y += 120;

            foreach (var p in Datos.Publicaciones)
            {
                Panel card = CrearCardPost(p, pnlFeed.Width - 20, y);
                pnlFeed.Controls.Add(card);
                y += card.Height + 10;
            }
        }

        private Panel CrearCardPost(Publicacion p, int w, int top)
        {
            int h = 130 + (p.Texto.Length > 80 ? 24 : 0);
            Panel card = CrearCard(w, h, 10, top);

            card.Paint += (s, e) =>
            {
                Graphics g = e.Graphics;
                g.SmoothingMode = SmoothingMode.AntiAlias;

                Rectangle rAv = new Rectangle(14, 14, 44, 44);
                using (LinearGradientBrush b = new LinearGradientBrush(rAv, Color.FromArgb(124, 58, 237), Color.FromArgb(236, 72, 153), 135f))
                    g.FillEllipse(b, rAv);
                g.DrawEllipse(new Pen(Color.White, 2), rAv);

                string ini = p.Username != null && p.Username.Length > 0 ? p.Username[0].ToString().ToUpper() : "?";
                using (Font fav = new Font("Segoe UI", 16, FontStyle.Bold))
                using (StringFormat sf = new StringFormat { Alignment = StringAlignment.Center, LineAlignment = StringAlignment.Center })
                {
                    g.DrawString(ini, fav, Brushes.White, rAv, sf);
                }


                using (Font fb = new Font("Segoe UI", 10, FontStyle.Bold))
                {
                    g.DrawString("@" + p.Username, fb, new SolidBrush(Color.FromArgb(107, 79, 160)), new PointF(66, 14));
                }

                using (Font fp = new Font("Segoe UI", 8))
                {
                    g.DrawString(p.FechaRelativa, fp, new SolidBrush(Color.FromArgb(176, 160, 204)), new PointF(66, 34));
                }


                using (StringFormat sf = new StringFormat())
                {
                    g.DrawString(p.Texto, new Font("Segoe UI", 10), new SolidBrush(Color.FromArgb(31, 10, 60)), new RectangleF(14, 66, w - 28, 50), sf);

                    g.DrawLine(new Pen(Color.FromArgb(237, 233, 248), 1), 14, h - 44, w - 14, h - 44);
                }


                Color likeColor = p.LikeadoPorMi ? Color.FromArgb(236, 72, 153) : Color.FromArgb(176, 160, 204);
                string likeStr = (p.LikeadoPorMi ? "♥" : "♡") + "  Me gusta  (" + p.Likes + ")";
                using (Font fl = new Font("Segoe UI", 9))
                    g.DrawString(likeStr, fl, new SolidBrush(likeColor), new PointF(14, h - 34));
            };

            Button btnLike = new Button
            {
                Left = 14,
                Top = h - 40,
                Width = 180,
                Height = 28,
                FlatStyle = FlatStyle.Flat,
                BackColor = Color.Transparent,
                Text = "",
                Cursor = Cursors.Hand
            };
            btnLike.FlatAppearance.BorderSize = 0;
            btnLike.FlatAppearance.MouseOverBackColor = Color.Transparent;
            btnLike.Click += (s, e) =>
            {
                if (!p.LikeadoPorMi)
                {
                    p.Likes++; p.LikeadoPorMi = true;
                }
                else
                {
                    p.Likes--; p.LikeadoPorMi = false;
                }
                card.Invalidate();
            };
            card.Controls.Add(btnLike);
            return card;
        }

        private void CargarPerfil()
        {
            pnlPerfil.Controls.Clear();
            var u = Datos.UsuarioActual;
            if (u == null)
            {
                return;
            }
            int y = 10, w = pnlPerfil.Width - 20;

            Panel cardInfo = CrearCard(w, 150, 10, y);
            cardInfo.Paint += (s, e) =>
            {
                var g = e.Graphics;
                g.SmoothingMode = SmoothingMode.AntiAlias;

                Rectangle rAv = new Rectangle(20, 20, 80, 80);
                using (LinearGradientBrush b = new LinearGradientBrush(rAv, Color.FromArgb(124, 58, 237), Color.FromArgb(236, 72, 153), 135f))
                {
                    g.FillEllipse(b, rAv);
                    g.DrawEllipse(new Pen(Color.White, 3), rAv);
                }


                using (Font f = new Font("Segoe UI", 28, FontStyle.Bold))
                using (StringFormat sf = new StringFormat { Alignment = StringAlignment.Center, LineAlignment = StringAlignment.Center })
                {
                    g.DrawString(u.Inicial, f, Brushes.White, rAv, sf);
                }


                using (Font f = new Font("Segoe UI", 14, FontStyle.Bold))
                {
                    g.DrawString("@" + u.Username, f, new SolidBrush(Color.FromArgb(31, 10, 60)), new PointF(114, 22));
                }

                using (Font f = new Font("Segoe UI", 10))
                {
                    g.DrawString(u.Email, f, new SolidBrush(Color.FromArgb(176, 160, 204)), new PointF(114, 52));
                }

                int pubs = Datos.Publicaciones.Count(p => p.IdUsuario == u.Id);
                using (Font f = new Font("Segoe UI", 10))
                {
                    g.DrawString("Publicaciones: " + pubs, f, new SolidBrush(Color.FromArgb(139, 92, 246)), new PointF(114, 76));
                }

                using (Font f = new Font("Segoe UI", 9))
                {
                    g.DrawString("Miembro desde: " + u.FechaRegistro.ToString("dd/MM/yyyy"), f, new SolidBrush(Color.FromArgb(107, 79, 160)), new PointF(114, 102));
                }
            };
            pnlPerfil.Controls.Add(cardInfo);
            y += 162;

            Label lbl = new Label
            {
                Text = "Mis publicaciones",
                Font = new Font("Segoe UI", 12, FontStyle.Bold),
                ForeColor = Color.FromArgb(31, 10, 60),
                Left = 10,
                Top = y,
                Width = w,
                Height = 28,
                BackColor = Color.Transparent
            };
            pnlPerfil.Controls.Add(lbl);
            y += 36;

            var misPubs = Datos.Publicaciones.FindAll(p => p.IdUsuario == u.Id);
            if (misPubs.Count == 0)
            {
                Label lbl2 = new Label
                {
                    Text = "Aun no has publicado nada.",
                    Font = new Font("Segoe UI", 10),
                    ForeColor = Color.FromArgb(176, 160, 204),
                    Left = 10,
                    Top = y,
                    Width = w,
                    Height = 28,
                    BackColor = Color.Transparent
                };
                pnlPerfil.Controls.Add(lbl2);
            }
            else
            {
                foreach (var p in misPubs)
                {
                    var c = CrearCardPost(p, w, y);
                    pnlPerfil.Controls.Add(c);
                    y += c.Height + 10;
                }
            }
        }

        private void CargarCuenta()
        {
            pnlCuenta.Controls.Clear();
            var u = Datos.UsuarioActual;
            if (u == null)
            {
                return;
            }
            int y = 10;
            int w = Math.Min(pnlCuenta.Width - 20, 480);

            Label lblTit = new Label
            {
                Text = "Mi Cuenta",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = Color.FromArgb(31, 10, 60),
                Left = 10,
                Top = y,
                Width = w,
                Height = 30,
                BackColor = Color.Transparent
            };
            pnlCuenta.Controls.Add(lblTit);
            y += 42;

            Panel cardU = CrearCard(w, 90, 10, y);
            cardU.Paint += (s, e) =>
            {
                var g = e.Graphics;
                g.SmoothingMode = SmoothingMode.AntiAlias;

                Rectangle rAv = new Rectangle(14, 14, 56, 56);
                using (LinearGradientBrush b = new LinearGradientBrush(rAv, Color.FromArgb(124, 58, 237), Color.FromArgb(236, 72, 153), 135f))
                {
                    g.FillEllipse(b, rAv);
                }
                g.DrawEllipse(new Pen(Color.White, 2), rAv);

                using (Font f = new Font("Segoe UI", 20, FontStyle.Bold))
                {
                    using (StringFormat sf = new StringFormat { Alignment = StringAlignment.Center, LineAlignment = StringAlignment.Center })
                    {
                        g.DrawString(u.Inicial, f, Brushes.White, rAv, sf);
                    }
                }

                using (Font f = new Font("Segoe UI", 12, FontStyle.Bold))
                {
                    g.DrawString("@" + u.Username, f, new SolidBrush(Color.FromArgb(31, 10, 60)), new PointF(82, 18));
                }

                using (Font f = new Font("Segoe UI", 10))
                {
                    g.DrawString(u.Email, f, new SolidBrush(Color.FromArgb(176, 160, 204)), new PointF(82, 46));
                }
            };
            pnlCuenta.Controls.Add(cardU);
            y += 106;

            // card para editar los datos
            Panel cardEdit = CrearCard(w, 280, 10, y);

            Label lblEdit = new Label
            {
                Text = "Editar datos",
                Font = new Font("Segoe UI", 12, FontStyle.Bold),
                ForeColor = Color.FromArgb(31, 10, 60),
                Left = 16,
                Top = 14,
                Width = w - 32,
                Height = 26,
                BackColor = Color.Transparent
            };

            TextBox tbUser = new TextBox
            {
                Left = 16,
                Top = 48,
                Width = w - 36,
                Height = 34,
                Font = new Font("Segoe UI", 10),
                BackColor = Color.FromArgb(250, 245, 255),
                ForeColor = Color.FromArgb(31, 10, 60),
                BorderStyle = BorderStyle.FixedSingle
            };

            TextBox tbMail = new TextBox
            {
                Left = 16,
                Top = 92,
                Width = w - 36,
                Height = 34,
                Font = new Font("Segoe UI", 10),
                BackColor = Color.FromArgb(250, 245, 255),
                ForeColor = Color.FromArgb(31, 10, 60),
                BorderStyle = BorderStyle.FixedSingle
            };

            TextBox tbPass = new TextBox
            {
                Left = 16,
                Top = 136,
                Width = w - 36,
                Height = 34,
                Font = new Font("Segoe UI", 10),
                BackColor = Color.FromArgb(250, 245, 255),
                ForeColor = Color.FromArgb(31, 10, 60),
                BorderStyle = BorderStyle.FixedSingle,
                PasswordChar = '*'
            };

            AplicarPlaceholder(tbUser, "Nuevo usuario (dejar vacio para no cambiar)");
            AplicarPlaceholder(tbMail, "Nuevo email (dejar vacio para no cambiar)");
            AplicarPlaceholder(tbPass, "Nueva contrasena (dejar vacio para no cambiar)");

            Label lblMsg = new Label
            {
                Left = 16,
                Top = 178,
                Width = w - 36,
                Height = 20,
                Font = new Font("Segoe UI", 9),
                BackColor = Color.Transparent,
                Visible = false
            };

            Button btnGuardar = new Button
            {
                Text = "Guardar cambios",
                Left = 16,
                Top = 204,
                Width = w - 36,
                Height = 44,
                Font = new Font("Segoe UI", 10, FontStyle.Bold),
                BackColor = Color.FromArgb(139, 92, 246),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnGuardar.FlatAppearance.BorderSize = 0;
            btnGuardar.Click += (s, e) =>
            {
                string u2 = tbUser.Text == "Nuevo usuario (dejar vacio para no cambiar)" ? "" : tbUser.Text.Trim();
                string m2 = tbMail.Text == "Nuevo email (dejar vacio para no cambiar)" ? "" : tbMail.Text.Trim();
                string p2 = tbPass.Text == "Nueva contrasena (dejar vacio para no cambiar)" ? "" : tbPass.Text.Trim();

                if (string.IsNullOrWhiteSpace(u2) && string.IsNullOrWhiteSpace(m2) && string.IsNullOrWhiteSpace(p2))
                {
                    lblMsg.Text = "Rellena al menos un campo";
                    lblMsg.ForeColor = Color.FromArgb(239, 68, 68);
                    lblMsg.Visible = true;
                    return;
                }

                Datos.EditarCuenta(u2, m2, p2);
                lblMsg.Text = "Cuenta actualizada correctamente";
                lblMsg.ForeColor = Color.FromArgb(16, 185, 129);
                lblMsg.Visible = true;
                tbUser.Text = "";
                tbMail.Text = "";
                tbPass.Text = "";
                cardU.Invalidate();
                pnlHeader.Invalidate();
            };

            cardEdit.Controls.AddRange(new Control[] { lblEdit, tbUser, tbMail, tbPass, lblMsg, btnGuardar });
            pnlCuenta.Controls.Add(cardEdit);
            y += 296;



            Button btnLogout = new Button
            {
                Text = "Cerrar sesion",
                Left = 10,
                Top = y,
                Width = w,
                Height = 46,
                Font = new Font("Segoe UI", 11, FontStyle.Bold),
                BackColor = Color.FromArgb(139, 92, 246),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnLogout.FlatAppearance.BorderSize = 0;
            btnLogout.Click += (s, e) => { Datos.Logout(); new FormLogin().Show(); this.Close(); };
            pnlCuenta.Controls.Add(btnLogout);
            y += 58;

            Button btnElim = new Button
            {
                Text = "Eliminar cuenta",
                Left = 10,
                Top = y,
                Width = w,
                Height = 46,
                Font = new Font("Segoe UI", 11, FontStyle.Bold),
                BackColor = Color.FromArgb(239, 68, 68),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnElim.FlatAppearance.BorderSize = 0;
            btnElim.Click += (s, e) =>
            {
                if (MessageBox.Show("Esta accion es irreversible. Eliminar cuenta?", "Eliminar",
                    MessageBoxButtons.YesNo, MessageBoxIcon.Warning) == DialogResult.Yes)
                {
                    Datos.EliminarCuenta();
                    new FormLogin().Show();
                    this.Close();
                }
            };
            pnlCuenta.Controls.Add(btnElim);
        }

        private Panel CrearCard(int w, int h, int left, int top)
        {
            Panel p = new Panel { Left = left, Top = top, Width = w, Height = h, BackColor = Color.White };
            p.Paint += (s, e) =>
            {
                var g = e.Graphics;
                g.SmoothingMode = SmoothingMode.AntiAlias;
                using (var pen = new Pen(Color.FromArgb(237, 233, 248), 1))
                {
                    var r = new Rectangle(0, 0, p.Width - 1, p.Height - 1);
                    int d = 32;
                    var path = new System.Drawing.Drawing2D.GraphicsPath();
                    path.AddArc(r.X, r.Y, d, d, 180, 90);
                    path.AddArc(r.Right - d, r.Y, d, d, 270, 90);
                    path.AddArc(r.Right - d, r.Bottom - d, d, d, 0, 90);
                    path.AddArc(r.X, r.Bottom - d, d, d, 90, 90);
                    path.CloseFigure();
                    g.FillPath(Brushes.White, path);
                    g.DrawPath(pen, path);
                }
            };
            return p;
        }

        private void btnFeed_Click(object sender, EventArgs e) { MostrarTab(0); CargarFeed(); }
        private void btnPerfil_Click(object sender, EventArgs e) { MostrarTab(1); CargarPerfil(); }
        private void btnCuenta_Click(object sender, EventArgs e) { MostrarTab(2); CargarCuenta(); }

        private void MostrarTab(int idx)
        {
            pnlFeed.Visible = idx == 0;
            pnlPerfil.Visible = idx == 1;
            pnlCuenta.Visible = idx == 2;

            btnFeed.BackColor = idx == 0 ? Color.FromArgb(252, 231, 243) : Color.White;
            btnPerfil.BackColor = idx == 1 ? Color.FromArgb(252, 231, 243) : Color.White;
            btnCuenta.BackColor = idx == 2 ? Color.FromArgb(252, 231, 243) : Color.White;

            btnFeed.ForeColor = idx == 0 ? Color.FromArgb(139, 92, 246) : Color.FromArgb(107, 79, 160);
            btnPerfil.ForeColor = idx == 1 ? Color.FromArgb(139, 92, 246) : Color.FromArgb(107, 79, 160);
            btnCuenta.ForeColor = idx == 2 ? Color.FromArgb(139, 92, 246) : Color.FromArgb(107, 79, 160);
        }

        private void AplicarPlaceholder(System.Windows.Forms.TextBox tb, string texto)
        {
            tb.Text = texto;
            tb.ForeColor = System.Drawing.Color.FromArgb(176, 160, 204);
            tb.Enter += (s, e) =>
            {
                if (tb.Text == texto)
                {
                    tb.Text = "";
                    tb.ForeColor = System.Drawing.Color.FromArgb(31, 10, 60);
                }
            };
            tb.Leave += (s, e) =>
            {
                if (string.IsNullOrWhiteSpace(tb.Text))
                {
                    tb.Text = texto;
                    tb.ForeColor = System.Drawing.Color.FromArgb(176, 160, 204);
                }
            };
        }

        private void pnlHeader_Paint(object sender, System.Windows.Forms.PaintEventArgs e)
        {
            System.Drawing.Graphics g = e.Graphics;
            g.SmoothingMode = System.Drawing.Drawing2D.SmoothingMode.AntiAlias;
            System.Drawing.Rectangle r = pnlHeader.ClientRectangle;

            using (System.Drawing.Drawing2D.LinearGradientBrush b = new System.Drawing.Drawing2D.LinearGradientBrush(
                r, System.Drawing.Color.FromArgb(124, 58, 237), System.Drawing.Color.FromArgb(236, 72, 153), 135f))
            {
                g.FillRectangle(b, r);
            }

            System.Drawing.Rectangle rAv = new System.Drawing.Rectangle(12, 10, 40, 40);
            using (System.Drawing.Drawing2D.LinearGradientBrush b = new System.Drawing.Drawing2D.LinearGradientBrush(
                rAv, System.Drawing.Color.FromArgb(109, 40, 217), System.Drawing.Color.FromArgb(219, 39, 119), 135f))
            {
                g.FillEllipse(b, rAv);
            }
            g.DrawEllipse(new System.Drawing.Pen(System.Drawing.Color.White, 2), rAv);

            string ini = Datos.UsuarioActual != null ? Datos.UsuarioActual.Inicial : "?";
            using (System.Drawing.Font f = new System.Drawing.Font("Segoe UI", 14, System.Drawing.FontStyle.Bold))
            {
                using (System.Drawing.StringFormat sf = new System.Drawing.StringFormat { Alignment = System.Drawing.StringAlignment.Center, LineAlignment = System.Drawing.StringAlignment.Center })
                {
                    g.DrawString(ini, f, System.Drawing.Brushes.White, rAv, sf);
                }
            }

            using (System.Drawing.Font f = new System.Drawing.Font("Segoe UI", 15, System.Drawing.FontStyle.Bold))
            {
                using (System.Drawing.StringFormat sf = new System.Drawing.StringFormat { LineAlignment = System.Drawing.StringAlignment.Center })
                {
                    g.DrawString("Mi Red Social", f, System.Drawing.Brushes.White, new System.Drawing.RectangleF(62, 0, 400, 60), sf);
                }
            }

            string uname = "@" + (Datos.UsuarioActual != null ? Datos.UsuarioActual.Username : "");
            using (System.Drawing.Font f = new System.Drawing.Font("Segoe UI", 9))
            {
                using (System.Drawing.StringFormat sf = new System.Drawing.StringFormat { Alignment = System.Drawing.StringAlignment.Far, LineAlignment = System.Drawing.StringAlignment.Center })
                {
                    using (System.Drawing.SolidBrush sb = new System.Drawing.SolidBrush(System.Drawing.Color.FromArgb(210, 255, 255, 255)))
                    {
                        g.DrawString(uname, f, sb, new System.Drawing.RectangleF(0, 0, pnlHeader.Width - 12, 60), sf);
                    }
                }
            }
        }

        private void pnlNav_Paint(object sender, System.Windows.Forms.PaintEventArgs e)
        {
            e.Graphics.DrawLine(new System.Drawing.Pen(System.Drawing.Color.FromArgb(237, 233, 248), 1),
                pnlNav.Width - 1, 0, pnlNav.Width - 1, pnlNav.Height);
        }

        private void pnlNav_SizeChanged(object sender, EventArgs e)
        {
            if (picGatoNav != null)
            {
                picGatoNav.Top = pnlNav.Height - 130;
            }

        }

        private void FormPrincipal_SizeChanged(object sender, EventArgs e)
        {
            int cw = this.ClientSize.Width - 180;
            int ch = this.ClientSize.Height - 60;
            if (cw > 0 && ch > 0)
            {
                pnlFeed.Size = pnlPerfil.Size = pnlCuenta.Size = new Size(cw, ch);
            }

        }
    }
}
