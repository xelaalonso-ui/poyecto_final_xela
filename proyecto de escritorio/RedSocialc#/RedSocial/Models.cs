using System;
using System.Collections.Generic;
using System.Linq;

namespace RedSocial
{
    public class Usuario
    {
        public int Id { get; set; }
        public string Username { get; set; }
        public string Email { get; set; }
        public string Password { get; set; }
        public DateTime FechaRegistro { get; set; }
        public string Inicial { get { return Username != null && Username.Length > 0 ? Username[0].ToString().ToUpper() : "?"; } }
    }

    public class Publicacion
    {
        public int Id { get; set; }
        public int IdUsuario { get; set; }
        public string Username { get; set; }
        public string Texto { get; set; }
        public DateTime Fecha { get; set; }
        public int Likes { get; set; }
        public bool LikeadoPorMi { get; set; }

        public string FechaRelativa
        {
            get
            {
                TimeSpan d = DateTime.Now - Fecha;
                if (d.TotalSeconds < 60) return "hace unos segundos";
                if (d.TotalMinutes < 60) return "hace " + (int)d.TotalMinutes + " min";
                if (d.TotalHours < 24) return "hace " + (int)d.TotalHours + " h";
                return "hace " + (int)d.TotalDays + " dias";
            }
        }
    }

    public static class DataStore
    {
        public static List<Usuario> Usuarios = new List<Usuario>();
        public static List<Publicacion> Publicaciones = new List<Publicacion>();
        public static Usuario UsuarioActual = null;

        private static int _uid = 1;
        private static int _pid = 1;

        static DataStore()
        {
            Usuarios.Add(new Usuario { Id = _uid++, Username = "maria", Email = "maria@demo.com", Password = "123456", FechaRegistro = DateTime.Now.AddDays(-30) });
            Usuarios.Add(new Usuario { Id = _uid++, Username = "carlos", Email = "carlos@demo.com", Password = "123456", FechaRegistro = DateTime.Now.AddDays(-20) });
            Usuarios.Add(new Usuario { Id = _uid++, Username = "ana", Email = "ana@demo.com", Password = "123456", FechaRegistro = DateTime.Now.AddDays(-10) });

            Publicaciones.Add(new Publicacion { Id = _pid++, IdUsuario = 1, Username = "maria", Texto = "Hola a todos! Primer dia aqui :)", Fecha = DateTime.Now.AddHours(-2), Likes = 5 });
            Publicaciones.Add(new Publicacion { Id = _pid++, IdUsuario = 2, Username = "carlos", Texto = "Como va el fin de semana? Fui a la montana!", Fecha = DateTime.Now.AddHours(-5), Likes = 3 });
            Publicaciones.Add(new Publicacion { Id = _pid++, IdUsuario = 3, Username = "ana", Texto = "Acabo de leer un libro increible, lo recomiendo!", Fecha = DateTime.Now.AddDays(-1), Likes = 8 });
            Publicaciones.Add(new Publicacion { Id = _pid++, IdUsuario = 1, Username = "maria", Texto = "Buenos dias! Que tengais un dia genial", Fecha = DateTime.Now.AddDays(-2), Likes = 12 });
        }

        public static bool Login(string email, string password)
        {
            Usuario u = Usuarios.Find(x => x.Email.Equals(email, StringComparison.OrdinalIgnoreCase) && x.Password == password);
            if (u == null) return false;
            UsuarioActual = u;
            return true;
        }

        public static bool Registrar(string username, string email, string password, out string error)
        {
            error = "";
            if (Usuarios.Exists(x => x.Email.Equals(email, StringComparison.OrdinalIgnoreCase)))
            { error = "Ese email ya esta en uso"; return false; }
            if (Usuarios.Exists(x => x.Username.Equals(username, StringComparison.OrdinalIgnoreCase)))
            { error = "Ese usuario ya esta en uso"; return false; }
            Usuarios.Add(new Usuario { Id = _uid++, Username = username, Email = email, Password = password, FechaRegistro = DateTime.Now });
            return true;
        }

        public static void Publicar(string texto)
        {
            if (UsuarioActual == null) return;
            Publicaciones.Insert(0, new Publicacion
            {
                Id = _pid++,
                IdUsuario = UsuarioActual.Id,
                Username = UsuarioActual.Username,
                Texto = texto,
                Fecha = DateTime.Now
            });
        }

        public static void EditarCuenta(string nuevoUsername, string nuevoEmail, string nuevaPassword)
        {
            if (UsuarioActual == null) return;
            if (!string.IsNullOrWhiteSpace(nuevoUsername)) UsuarioActual.Username = nuevoUsername;
            if (!string.IsNullOrWhiteSpace(nuevoEmail)) UsuarioActual.Email = nuevoEmail;
            if (!string.IsNullOrWhiteSpace(nuevaPassword)) UsuarioActual.Password = nuevaPassword;
        }

        public static void EliminarCuenta()
        {
            if (UsuarioActual == null) return;
            Publicaciones.RemoveAll(p => p.IdUsuario == UsuarioActual.Id);
            Usuarios.Remove(UsuarioActual);
            UsuarioActual = null;
        }

        public static void Logout() { UsuarioActual = null; }
    }
}
