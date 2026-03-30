using System;
using System.Collections.Generic;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Windows.Forms;

namespace RedSocial
{
    public class FondoAnimado : Panel
    {
        private Timer _timer;
        private List<Burbuja> _burbujas = new List<Burbuja>();
        private float _gradOffset = 0f;
        private Random _rnd = new Random();

        private class Burbuja
        {
            public float X, Y, Radio, VelX, VelY, Opacidad;
        }

        public FondoAnimado()
        {
            this.Dock = DockStyle.Fill;
            this.DoubleBuffered = true;

            for (int i = 0; i < 18; i++)
                CrearBurbuja(true);

            _timer = new Timer { Interval = 30 };
            _timer.Tick += (s, e) => Animar();
            _timer.Start();
        }

        private void CrearBurbuja(bool random)
        {
            int w = this.Width > 0 ? this.Width : 500;
            int h = this.Height > 0 ? this.Height : 600;
            _burbujas.Add(new Burbuja {
                X = random ? _rnd.Next(0, w) : _rnd.Next(0, w),
                Y = random ? _rnd.Next(0, h) : h + 20,
                Radio = _rnd.Next(8, 32),
                VelX = (float)(_rnd.NextDouble() - 0.5) * 0.8f,
                VelY = -(float)(_rnd.NextDouble() * 0.6 + 0.3),
                Opacidad = (float)(_rnd.NextDouble() * 0.25 + 0.08)
            });
        }

        private void Animar()
        {
            _gradOffset += 0.005f;
            if (_gradOffset > 1f) _gradOffset = 0f;

            int w = this.Width > 0 ? this.Width : 1;
            int h = this.Height > 0 ? this.Height : 1;

            for (int i = _burbujas.Count - 1; i >= 0; i--)
            {
                var b = _burbujas[i];
                b.X += b.VelX;
                b.Y += b.VelY;
                if (b.Y < -40 || b.X < -40 || b.X > w + 40)
                {
                    _burbujas.RemoveAt(i);
                    CrearBurbuja(false);
                }
            }
            this.Invalidate();
        }

        protected override void OnPaint(PaintEventArgs e)
        {
            Graphics g = e.Graphics;
            g.SmoothingMode = SmoothingMode.AntiAlias;

            int w = this.Width;
            int h = this.Height;

            float t = _gradOffset;
            Color c1 = LerpColor(Color.FromArgb(243, 232, 255), Color.FromArgb(252, 231, 243), t);
            Color c2 = LerpColor(Color.FromArgb(224, 231, 255), Color.FromArgb(243, 232, 255), t);
            Color c3 = LerpColor(Color.FromArgb(252, 231, 243), Color.FromArgb(224, 231, 255), t);

            using (LinearGradientBrush bg = new LinearGradientBrush(
                new Rectangle(0, 0, w, h), c1, c2, 135f))
            {
                ColorBlend blend = new ColorBlend(3);
                blend.Colors = new Color[] { c1, c3, c2 };
                blend.Positions = new float[] { 0f, 0.5f, 1f };
                bg.InterpolationColors = blend;
                g.FillRectangle(bg, 0, 0, w, h);
            }

            foreach (var b in _burbujas)
            {
                int alpha = (int)(b.Opacidad * 255);
                Color colorBurbuja = LerpColor(
                    Color.FromArgb(alpha, 139, 92, 246),
                    Color.FromArgb(alpha, 236, 72, 153),
                    (float)(b.X / (w > 0 ? w : 1)));

                using (SolidBrush br = new SolidBrush(colorBurbuja))
                    g.FillEllipse(br, b.X - b.Radio, b.Y - b.Radio, b.Radio * 2, b.Radio * 2);

                using (Pen pen = new Pen(Color.FromArgb(alpha / 2, 255, 255, 255), 1f))
                    g.DrawEllipse(pen, b.X - b.Radio, b.Y - b.Radio, b.Radio * 2, b.Radio * 2);
            }
        }

        private Color LerpColor(Color a, Color b, float t)
        {
            t = Math.Max(0, Math.Min(1, t));
            return Color.FromArgb(
                (int)(a.A + (b.A - a.A) * t),
                (int)(a.R + (b.R - a.R) * t),
                (int)(a.G + (b.G - a.G) * t),
                (int)(a.B + (b.B - a.B) * t));
        }

        protected override void Dispose(bool disposing)
        {
            if (disposing) _timer?.Stop();
            base.Dispose(disposing);
        }
    }
}
