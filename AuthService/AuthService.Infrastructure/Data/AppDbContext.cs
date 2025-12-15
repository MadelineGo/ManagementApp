using Microsoft.EntityFrameworkCore;

namespace AuthService.Infrastructure.Data;

public class AppDbContext(DbContextOptions<AppDbContext> options) : DbContext(options)
{
    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);
        modelBuilder.ApplyConfigurationsFromAssembly(typeof(AppDbContext).Assembly);
    }
}

//dotnet ef migrations add InitialDB -c AppDbContext -p AuthService.Infrastructure -s AuthService.Api -o Data/Migrations
//dotnet ef migrations add AddPreviousLastLoginToUser -p AuthService.Infrastructure -s AuthService.Api -o Data/Migrations

//dotnet ef database update -c AppDbContext -p AuthService.Infrastructure -s AuthService.Api
//dotnet ef migrations remove -c AppDbContext -p AuthService.Infrastructure -s AuthService.Api  