using AuthService.Domain.Models;
using AuthService.Domain.Repositories;
using AuthService.Infrastructure.Data;
using Microsoft.EntityFrameworkCore;

namespace AuthService.Infrastructure.Repositories;

public class UserRepository(AppDbContext context) : IUserRepository
{
    private readonly DbSet<User> _users = context.Set<User>();

    public async Task AddAsync(User user)
    {
        ArgumentNullException.ThrowIfNull(user);
        await _users.AddAsync(user);
        await context.SaveChangesAsync();
    }

    public async Task<User?> FindByEmailAsync(string email)
    {
        return await _users.FirstOrDefaultAsync(x => x.Email == email);
    }

    public async Task UpdateAsync(User user)
    {
        _users.Update(user);
        await context.SaveChangesAsync();
    }
    
    public async Task<User?> FindByIdAsync(int userId)
    {
        return await _users.FirstOrDefaultAsync(x => x.Id == userId);
    }

}