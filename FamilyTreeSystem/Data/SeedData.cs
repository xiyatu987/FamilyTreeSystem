using FamilyTreeSystem.Models;
using Microsoft.EntityFrameworkCore;

namespace FamilyTreeSystem.Data
{
    public static class SeedData
    {
        public static void Initialize(FamilyTreeContext context)
        {
            // 检查是否已经有用户数据
            if (context.Users.Any())
            {
                return; // 数据库已经有种子数据
            }

            // 添加默认用户
            var defaultUser = new User
            {
                Name = "管理员",
                Email = "admin@example.com",
                Password = "admin123", // 在实际应用中应该使用密码哈希
                Role = "admin",
                RememberToken = ""
            };

            context.Users.Add(defaultUser);
            context.SaveChanges();

            // 添加测试家族成员数据
            var members = new List<FamilyMember>
            {
                new FamilyMember
                {
                    FullName = "张三",
                    Gender = "Male",
                    Generation = 1,
                    BirthOrder = 1,
                    IsAlive = true,
                    UserId = defaultUser.Id
                },
                new FamilyMember
                {
                    FullName = "李四",
                    Gender = "Female",
                    Generation = 1,
                    IsAlive = true,
                    UserId = defaultUser.Id
                },
                new FamilyMember
                {
                    FullName = "张大三",
                    Gender = "Male",
                    Generation = 2,
                    BirthOrder = 1,
                    FatherId = 1,
                    MotherId = 2,
                    IsAlive = true,
                    UserId = defaultUser.Id
                },
                new FamilyMember
                {
                    FullName = "张小三",
                    Gender = "Male",
                    Generation = 2,
                    BirthOrder = 2,
                    FatherId = 1,
                    MotherId = 2,
                    IsAlive = true,
                    UserId = defaultUser.Id
                },
                new FamilyMember
                {
                    FullName = "王五",
                    Gender = "Female",
                    Generation = 2,
                    IsAlive = true,
                    SpouseId = 3,
                    UserId = defaultUser.Id
                }
            };

            context.FamilyMembers.AddRange(members);
            context.SaveChanges();
        }
    }
}