using System.Windows.Forms;
using System.Drawing;

namespace RedSocial
{
    public static class ValidateTextbox
    {
        public static bool EsVacio(TextBox tb)
        {
            return string.IsNullOrWhiteSpace(tb.Text);
        }

        public static bool LongitudMinima(TextBox tb, int min)
        {
            return tb.Text.Trim().Length >= min;
        }

        // validacion de email basica, no hace falta mas
        public static bool EsEmail(TextBox tb)
        {
            return tb.Text.Contains("@") && tb.Text.Contains(".");
        }

        public static void MarcarError(TextBox tb)
        {
            tb.BackColor = Color.FromArgb(255, 230, 230);
        }

        public static void MarcarOk(TextBox tb)
        {
            tb.BackColor = Color.FromArgb(250, 245, 255);
        }

        public static bool ValidarRequerido(TextBox tb, Label lblError, string mensaje)
        {
            if (EsVacio(tb))
            {
                MarcarError(tb);
                lblError.Text = mensaje;
                lblError.Visible = true;
                return false;
            }
            MarcarOk(tb);
            return true;
        }
    }
}
