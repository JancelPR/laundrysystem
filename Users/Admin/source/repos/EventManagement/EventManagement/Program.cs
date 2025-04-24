using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using EventManagement.Data;
using EventManagement.Areas.Identity.Data;
namespace EventManagement
{
    public class Program
    {
        public static void Main(string[] args)
        {
            var builder = WebApplication.CreateBuilder(args);
            var connectionString = builder.Configuration.GetConnectionString("EventManagement") ?? throw new InvalidOperationException("Connection string 'EventManagement' not found.");

            builder.Services.AddDbContext<AuthDbContext>(options => options.UseSqlServer(connectionString));

            builder.Services.AddDefaultIdentity<EventUser>(options => options.SignIn.RequireConfirmedAccount = false).AddEntityFrameworkStores<AuthDbContext>();

            // Add services to the container.
            builder.Services.AddControllersWithViews();
            builder.Services.AddRazorPages();

            var app = builder.Build();
            app.MapRazorPages();

            // Configure the HTTP request pipeline.
            if (!app.Environment.IsDevelopment())
            {
                app.UseExceptionHandler("/Home/Error");
                // The default HSTS value is 30 days. You may want to change this for production scenarios, see https://aka.ms/aspnetcore-hsts.
                app.UseHsts();
            }

            app.UseHttpsRedirection();
            app.UseStaticFiles();

            app.UseRouting();

            app.UseAuthorization();

            app.MapControllerRoute(
                name: "default",
                pattern: "{controller=Home}/{action=Index}/{id?}");

            app.Run();
        }
    }
}
