using AuthService.Domain.Models;

namespace AuthService.Domain.Repositories;

public interface IUserRepository
{
    Task AddAsync(User user);
    Task<User?> FindByEmailAsync(string email);
    Task UpdateAsync(User user);
    Task<User?> FindByIdAsync(int userId);
    
}