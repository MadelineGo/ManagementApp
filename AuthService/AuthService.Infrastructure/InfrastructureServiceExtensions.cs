using AuthService.Application.Services;
using AuthService.Domain.Repositories;
using AuthService.Domain.Services;
using AuthService.Infrastructure.Data;
using AuthService.Infrastructure.Repositories;
using AuthService.Infrastructure.Services;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.EntityFrameworkCore;

namespace AuthService.Infrastructure;

public static class InfrastructureServiceExtensions
{
    public static IServiceCollection AddInfrastructureLayer(this IServiceCollection services,
        IConfiguration configuration)
    {
        services.AddDbContext<AppDbContext>((sp, options) =>
            {
                // Try to build connection string from environment variables first
                var dbHost = configuration["DB_HOST"];
                var dbPort = configuration["DB_PORT"];
                var dbDatabase = configuration["DB_DATABASE"];
                var dbUsername = configuration["DB_USERNAME"];
                var dbPassword = configuration["DB_PASSWORD"];
                
                string connectionString;
                
                // If all environment variables are present, build connection string from them
                if (!string.IsNullOrEmpty(dbHost) && !string.IsNullOrEmpty(dbPort) && 
                    !string.IsNullOrEmpty(dbDatabase) && !string.IsNullOrEmpty(dbUsername) && 
                    !string.IsNullOrEmpty(dbPassword))
                {
                    connectionString = $"Server={dbHost},{dbPort};Database={dbDatabase};User Id={dbUsername};Password={dbPassword};TrustServerCertificate=True";
                }
                else
                {
                    // Fallback to connection string from appsettings.json
                    connectionString = configuration.GetConnectionString("DefaultConnection")!;
                }
                
                options.UseSqlServer(connectionString);
            }
        );
        
        services.AddScoped<IUserRepository, UserRepository>();
        services.AddScoped<IPasswordService, PasswordService>();
        services.AddScoped<IJwtService, JwtService>();
        
        return services;
    }
}