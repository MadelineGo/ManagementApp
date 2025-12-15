using System.Security.Claims;
using AuthService.Api.Contracts.Requests;
using AuthService.Application.Commands.Login;
using AuthService.Application.Commands.Register;
using AuthService.Application.Queries.GetUserInfo;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using AuthService.Application.Commands.Logout;

namespace AuthService.Api.Controllers;

[ApiController]
[Route("api/auth")]
public class AuthController(IMediator mediator) : ControllerBase
{
    /// <summary>
    /// Registra un nuevo usuario en el sistema.
    /// </summary>
    [HttpPost("register")] // Endpoint: POST /api/auth/register 
    [ProducesResponseType(StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> Register([FromBody] RegisterRequest request)
    {
        if (!ModelState.IsValid)
        {
            return BadRequest(ModelState);
        }
        
        var command = new RegisterCommand(
            request.Name, 
            request.LastName, 
            request.Email, 
            request.Password
            );
        
        var result = await mediator.Send(command);

        return CreatedAtAction(nameof(Register), result);
    }
    
    /// <summary>
    /// Inicia sesión de un usuario y retorna el JWT.
    /// </summary>
    [HttpPost("login")] // Endpoint: POST /api/auth/login
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    public async Task<IActionResult> Login([FromBody] LoginRequest request)
    {
        if (!ModelState.IsValid)
        {
            return BadRequest(ModelState);
        }

        var command = new LoginCommand(
            request.Email, 
            request.Password
        );
    
        try
        {
            var result = await mediator.Send(command);
            return Ok(result);
        }
        catch (ApplicationException ex)
        {
            return Unauthorized(new { message = ex.Message });
        }
        catch (Exception)
        {
            return StatusCode(StatusCodes.Status500InternalServerError, "An unexpected error occurred during login.");
        }
    }
    
    /// <summary>
    /// Cierra la sesión del usuario (invalidación del lado del cliente).
    /// </summary>
    [HttpPost("logout")] // Endpoint: POST /api/auth/logout
    [Authorize]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    public async Task<IActionResult> Logout()
    {
        await mediator.Send(new LogoutCommand());
        return Ok(new { message = "Logout successful" });
    }

    [Authorize]
    [HttpGet("info")] 
    public async Task<IActionResult> GetUserInfo()
    {
        // Obtener el UserId del claim del JWT ya validado por el middleware
        var userIdString = User.FindFirstValue(ClaimTypes.NameIdentifier); 
    
        if (int.TryParse(userIdString, out int userId))
        {
            var query = new GetUserInfoQuery(userId);
            var result = await mediator.Send(query);
            return Ok(result);
        }
    
        return Unauthorized();
    }
}