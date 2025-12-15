using System.ComponentModel.DataAnnotations;

namespace AuthService.Api.Contracts.Requests;

public record LoginRequest
{
    [Required, EmailAddress]
    public required string Email { get; init; } 
    
    [Required]   
    public required string Password { get; init; }
}