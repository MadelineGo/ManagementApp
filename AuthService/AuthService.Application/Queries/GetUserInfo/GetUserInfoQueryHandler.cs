using AuthService.Application.Dtos;
using AuthService.Domain.Repositories;
using MediatR;

namespace AuthService.Application.Queries.GetUserInfo;

public record GetUserInfoQuery(int UserId)  : IRequest<UserInfo>;

public class GetUserInfoQueryHandler(IUserRepository userRepository) : IRequestHandler<GetUserInfoQuery, UserInfo>
{
    public async Task<UserInfo> Handle(GetUserInfoQuery request, CancellationToken cancellationToken)
    {
        var user = await userRepository.FindByIdAsync(request.UserId); 

        return user is null ? throw new ApplicationException("User not found.") : new UserInfo(user.Id, user.Name, user.LastName, user.Username, user.Email);
    }
}