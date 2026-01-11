using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace FamilyTreeSystem.Migrations
{
    /// <inheritdoc />
    public partial class RemoveAncestralHallGraveMigrationFeatures : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "AncestralHallFamilyMember");

            migrationBuilder.DropTable(
                name: "Graves");

            migrationBuilder.DropTable(
                name: "MigrationRecords");

            migrationBuilder.DropTable(
                name: "AncestralHalls");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "AncestralHalls",
                columns: table => new
                {
                    Id = table.Column<int>(type: "INTEGER", nullable: false)
                        .Annotation("Sqlite:Autoincrement", true),
                    ClanId = table.Column<int>(type: "INTEGER", nullable: true),
                    UserId = table.Column<int>(type: "INTEGER", nullable: false),
                    BuiltDate = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Description = table.Column<string>(type: "TEXT", nullable: true),
                    Location = table.Column<string>(type: "TEXT", maxLength: 255, nullable: false),
                    Name = table.Column<string>(type: "TEXT", maxLength: 255, nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AncestralHalls", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AncestralHalls_Clans_ClanId",
                        column: x => x.ClanId,
                        principalTable: "Clans",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.SetNull);
                    table.ForeignKey(
                        name: "FK_AncestralHalls_Users_UserId",
                        column: x => x.UserId,
                        principalTable: "Users",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Graves",
                columns: table => new
                {
                    Id = table.Column<int>(type: "INTEGER", nullable: false)
                        .Annotation("Sqlite:Autoincrement", true),
                    MemberId = table.Column<int>(type: "INTEGER", nullable: false),
                    UserId = table.Column<int>(type: "INTEGER", nullable: false),
                    BurialDate = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Coordinates = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Description = table.Column<string>(type: "TEXT", nullable: true),
                    Location = table.Column<string>(type: "TEXT", maxLength: 255, nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Graves", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Graves_FamilyMembers_MemberId",
                        column: x => x.MemberId,
                        principalTable: "FamilyMembers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_Graves_Users_UserId",
                        column: x => x.UserId,
                        principalTable: "Users",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "MigrationRecords",
                columns: table => new
                {
                    Id = table.Column<int>(type: "INTEGER", nullable: false)
                        .Annotation("Sqlite:Autoincrement", true),
                    MemberId = table.Column<int>(type: "INTEGER", nullable: false),
                    UserId = table.Column<int>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Description = table.Column<string>(type: "TEXT", nullable: true),
                    FromLocation = table.Column<string>(type: "TEXT", maxLength: 255, nullable: false),
                    MigrationDate = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Reason = table.Column<string>(type: "TEXT", nullable: true),
                    ToLocation = table.Column<string>(type: "TEXT", maxLength: 255, nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MigrationRecords", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MigrationRecords_FamilyMembers_MemberId",
                        column: x => x.MemberId,
                        principalTable: "FamilyMembers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_MigrationRecords_Users_UserId",
                        column: x => x.UserId,
                        principalTable: "Users",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AncestralHallFamilyMember",
                columns: table => new
                {
                    AncestralHallsId = table.Column<int>(type: "INTEGER", nullable: false),
                    FamilyMembersId = table.Column<int>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AncestralHallFamilyMember", x => new { x.AncestralHallsId, x.FamilyMembersId });
                    table.ForeignKey(
                        name: "FK_AncestralHallFamilyMember_AncestralHalls_AncestralHallsId",
                        column: x => x.AncestralHallsId,
                        principalTable: "AncestralHalls",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_AncestralHallFamilyMember_FamilyMembers_FamilyMembersId",
                        column: x => x.FamilyMembersId,
                        principalTable: "FamilyMembers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_AncestralHallFamilyMember_FamilyMembersId",
                table: "AncestralHallFamilyMember",
                column: "FamilyMembersId");

            migrationBuilder.CreateIndex(
                name: "IX_AncestralHalls_ClanId",
                table: "AncestralHalls",
                column: "ClanId");

            migrationBuilder.CreateIndex(
                name: "IX_AncestralHalls_UserId",
                table: "AncestralHalls",
                column: "UserId");

            migrationBuilder.CreateIndex(
                name: "IX_Graves_MemberId",
                table: "Graves",
                column: "MemberId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_Graves_UserId",
                table: "Graves",
                column: "UserId");

            migrationBuilder.CreateIndex(
                name: "IX_MigrationRecords_MemberId",
                table: "MigrationRecords",
                column: "MemberId");

            migrationBuilder.CreateIndex(
                name: "IX_MigrationRecords_UserId",
                table: "MigrationRecords",
                column: "UserId");
        }
    }
}
